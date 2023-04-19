<?php

namespace app\Services;

use App\Models\Song;
use App\Services\StorageManager;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use ProtoneMedia\LaravelFFMpeg\Exporters\EncodingException;



class SongManager
{
    private ?Song $song = null;

    private $disk;

    private $directory = "song_src";

    private $raw_directory = "song_raw";

    public function __construct(Song $song)
    {
        $this->song = $song;
        $this->disk = StorageManager::getDisk();
    }

    public function convert($file)
    {
        if ($this->song == null) {
            throw new \Exception("Song is null");
        }

        if ($file == null) {
            throw new \Exception("File is null");
        }

        try {
            $savePath = StorageManager::saveFilePath($this->directory);
            $filename = $this->raw_directory . "/" . date("YmdHi") . $file->getClientOriginalName();
            $final_filepath = $savePath . $this->song->song_id . ".ogg";
            Storage::disk('local')
                ->put($filename, file_get_contents($file));
            FFMpeg::fromDisk($this->disk)
                ->open($filename)
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
            $this->song->file_path = $final_filepath;
            FFMpeg::cleanupTemporaryFiles();
            return $this;
        } catch (EncodingException $exception) {
            $command = $exception->getCommand();
            $errorLog = $exception->getErrorOutput();
            dd($command, $errorLog);
        }
    }


    public function stream()
    {
        if ($this->song == null) {
            throw new \Exception("Song is null");
        }

        $path = $this->song->file_path;
        $file_name = $this->song->title . ".ogg";

        $storage = \Storage::disk($this->disk);
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
        return $this;
    }

    public function removeFile($filepath)
    {
        if (StorageManager::getDisk() == "local") {
            Storage::disk("local")->delete($filepath);
        }
        if (StorageManager::getDisk() == "s3") {
            Storage::disk("s3")->delete($filepath);
        }
    }
}
