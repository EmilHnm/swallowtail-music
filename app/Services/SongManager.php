<?php

namespace app\Services;

use App\Models\Song;
use App\Models\SongMetadata;
use App\Enum\SongMetadataStatusEnum;
use App\Services\StorageManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Events\SongConvertedSuccessFull;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use App\Listeners\SendSongConvertedSuccessFullListener;
use ProtoneMedia\LaravelFFMpeg\Exporters\EncodingException;



class SongManager
{
    private ?Song $song = null;

    private ?SongMetadata $SongMetadata = null;

    private $disk;

    private $directory = "song_src";

    private $raw_directory = "song_raw";

    public function __construct(Song $song)
    {
        $this->song = $song;
        if ($song_file = SongMetadata::where("song_id", $song->song_id)->first()) {
            $this->SongMetadata = $song_file;
        } else {
            $this->SongMetadata = new SongMetadata();
            $this->SongMetadata->song_id = $song->song_id;
            $this->SongMetadata->status = SongMetadataStatusEnum::PENDING;
            $this->SongMetadata->save();
        }
        $this->disk = StorageManager::getDisk();
    }

    public function convert($file_path)
    {
        $file = Storage::disk($this->disk)->get($file_path);
        if ($this->song == null) {
            throw new \Exception("Song is null");
        }

        if ($file == null) {
            throw new \Exception("File is null");
        }

        try {
            $this->SongMetadata->status = SongMetadataStatusEnum::PROCESSING;
            $savePath = StorageManager::saveFilePath($this->directory);

            $filename = $this->raw_directory . "/" . date("YmdHi");
            $final_filepath = $savePath . $this->song->song_id . ".ogg";

            $codec = FFMpeg::fromDisk($this->disk)
                ->open($file_path)
                ->getVideoStream()
                ->get('codec_name');
            if ($codec == "vorbis") {
                Storage::disk($this->disk)->move($file_path, $final_filepath);
                $this->removeFile($filename);
            } else {
                FFMpeg::fromDisk($this->disk)
                    ->open($file_path)
                    ->export()
                    ->addFilter([
                        "-strict",
                        "-2",
                        "-acodec",
                        "vorbis",
                        "-b:a",
                        "320k",
                    ])
                    ->save($final_filepath);
            }

            $this->removeFile($filename);
            $duration = FFMpeg::fromDisk($this->disk)
                ->open($final_filepath)
                ->getDurationInSeconds();
            $this->song->duration = $duration;
            $this->SongMetadata->status = SongMetadataStatusEnum::DONE;
            $this->SongMetadata->file_path = $final_filepath;
            $this->SongMetadata->driver = $this->disk;
            FFMpeg::cleanupTemporaryFiles();
            event(new SongConvertedSuccessFull($this->song->user, $this->song));
            return $this;
        } catch (EncodingException $exception) {
            $command = $exception->getCommand();
            $errorLog = $exception->getErrorOutput();
            $this->SongMetadata->status = SongMetadataStatusEnum::ERROR;
            $this->SongMetadata->save();
            Log::error($command . ' meet error: ' . '$errorLog',);
            throw new \Exception($exception->getMessage());
        }
    }


    public function stream()
    {
        if ($this->song == null) {
            throw new \Exception("Song is null");
        }

        $path = $this->SongMetadata->file_path;
        $file_name = $this->song->title . ".ogg";

        $storage = \Storage::disk($this->SongMetadata->driver);
        $fs = $storage->getDriver();
        return $storage->response($path, $file_name, [
            'Content-Type' => $fs->mimeType($path),
            'Content-Length' => $storage->size($path),
            'Content-Disposition' => 'inline; filename="' . $file_name . '"',
        ]);
    }

    public function save()
    {
        if ($this->song == null) {
            throw new \Exception("Song is null");
        }
        $this->song->save();
        $this->SongMetadata->save();
        return $this;
    }

    public function removeFile($filepath = null)
    {
        if ($filepath == null) {
            $filepath = $this->SongMetadata->file_path;
            $this->SongMetadata->delete();
        }
        if (StorageManager::getDisk() == "local") {
            Storage::disk("local")->delete($filepath);
        }
        if (StorageManager::getDisk() == "s3") {
            Storage::disk("s3")->delete($filepath);
        }
    }
}
