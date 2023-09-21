<?php

namespace app\Services;

use App\Models\Song;
use App\Models\SongFile;
use App\Services\StorageManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use ProtoneMedia\LaravelFFMpeg\Exporters\EncodingException;



class SongManager
{
    private ?Song $song = null;

    private ?SongFile $songFile = null;

    private $disk;

    private $directory = "song_src";

    private $raw_directory = "song_raw";

    public function __construct(Song $song)
    {
        $this->song = $song;
        if ($song_file = SongFile::where("song_id", $song->song_id)->first()) {
            $this->songFile = $song_file;
        } else {
            $this->songFile = new SongFile();
            $this->songFile->song_id = $song->song_id;
            $this->songFile->status = "pending";
            $this->songFile->save();
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
            $this->songFile->status = "processing";
            $savePath = StorageManager::saveFilePath($this->directory);

            $filename = $this->raw_directory . "/" . date("YmdHi");
            $final_filepath = $savePath . $this->song->song_id . ".ogg";
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
            $this->removeFile($filename);
            $duration = FFMpeg::fromDisk($this->disk)
                ->open($final_filepath)
                ->getDurationInSeconds();
            $this->song->duration = $duration;
            $this->songFile->status = "done";
            $this->songFile->file_path = $final_filepath;
            $this->songFile->driver = $this->disk;
            FFMpeg::cleanupTemporaryFiles();
            return $this;
        } catch (EncodingException $exception) {
            $command = $exception->getCommand();
            $errorLog = $exception->getErrorOutput();
            Log::error($command . ' meet error: ' . '$errorLog',);
        }
    }


    public function stream()
    {
        if ($this->song == null) {
            throw new \Exception("Song is null");
        }

        $path = $this->songFile->file_path;
        $file_name = $this->song->title . ".ogg";

        $storage = \Storage::disk($this->songFile->driver);
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
        $this->songFile->save();
        return $this;
    }

    public function removeFile($filepath = null)
    {
        if ($filepath == null) {
            $filepath = $this->songFile->file_path;
            $this->songFile->delete();
        }
        if (StorageManager::getDisk() == "local") {
            Storage::disk("local")->delete($filepath);
        }
        if (StorageManager::getDisk() == "s3") {
            Storage::disk("s3")->delete($filepath);
        }
    }
}
