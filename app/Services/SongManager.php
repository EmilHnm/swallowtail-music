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
        $file = Storage::disk('raws_audio')->get($file_path);
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

            $codec = FFMpeg::fromDisk('raws_audio')
                ->open($file_path)
                ->getVideoStream()
                ->get('codec_name');
            if ($codec == "vorbis") {
                Storage::disk('final_audio')->writeStream(
                    $final_filepath,
                    Storage::disk('raws_audio')->readStream($file_path)
                );
                Storage::disk('raws_audio')->delete($file_path);
            } else {
                FFMpeg::fromDisk('raws_audio')
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
                    ->toDisk('final_audio')
                    ->save($final_filepath);
                Storage::disk('raws_audio')->delete($file_path);
            }

            $duration = FFMpeg::fromDisk('final_audio')
                ->open($final_filepath)
                ->getDurationInSeconds();
            $this->SongMetadata->duration = $duration;
            $this->SongMetadata->status = SongMetadataStatusEnum::DONE;
            $this->SongMetadata->file_path = $final_filepath;
            $this->SongMetadata->driver = 'final_audio';
            $this->SongMetadata->size = Storage::disk('final_audio')->size($final_filepath);
            $this->SongMetadata->hash = hash("md5", Storage::disk('final_audio')->get($final_filepath));
            FFMpeg::cleanupTemporaryFiles();
            event(new SongConvertedSuccessFull($this->song->user, $this->song));
            return $this;
        } catch (EncodingException $exception) {
            $command = $exception->getCommand();
            $errorLog = $exception->getErrorOutput();
            $this->SongMetadata->status = SongMetadataStatusEnum::ERROR;
            $this->SongMetadata->save();
            Log::error($command . ' meet error: ' . "$errorLog");
            throw new \Exception("Song Manager convert error: " . $exception->getMessage());
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

    public function generateDownloadUrl($timeout = 600) {
        if ($this->song == null) {
            throw new \Exception("Song is null");
        }
        $path = $this->SongMetadata->file_path;
        $storage = \Storage::disk($this->SongMetadata->driver);
        $url = $storage->temporaryUrl($path, now()->addSeconds($timeout));
        return $url;
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
            $driver = $this->SongMetadata->driver;
            Storage::disk($driver)->delete($filepath);
            $this->SongMetadata->delete();
        }
    }
}
