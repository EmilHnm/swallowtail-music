<?php

use App\Models\Song;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use ProtoneMedia\LaravelFFMpeg\Exporters\EncodingException;



class SongManager
{
    private ?Song $song = null;

    private $disk;

    private $directory = "upload/song_src";

    public function __construct(Song $song)
    {
        $this->song = $song;
        $this->disk = StorageManager::getDisk();
    }

    public function convert($file)
    {
        if ($this->song == null) {
            throw new Exception("Song is null");
        }

        if ($file == null) {
            throw new Exception("File is null");
        }

        try {
            $filename = date("YmdHi") . $file->getClientOriginalName();
            Storage::disk($this->disk)->put($filename, file_get_contents($file));
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
                ->save($this->song->song_id . ".ogg");
            $this->removeFile($filename);
            $duration = FFMpeg::fromDisk($this->disk)
                ->open($this->song->song_id . ".ogg")
                ->getDurationInSeconds();
            $this->song->duration = $duration;
            FFMpeg::cleanupTemporaryFiles();
            return $this;
        } catch (EncodingException $exception) {
            $command = $exception->getCommand();
            $errorLog = $exception->getErrorOutput();
            dd($command, $errorLog);
        }
    }

    private function getSaveablePath()
    {
        $directories = Storage::directories($this->directory);
        $count_1 = count($directories);

        if ($count_1 == 0) {
            Storage::makeDirectory($this->directory . "/000/000");
            return $this->directory . "/000/000";
        }
        $sub_directories_str = $this->directory . '/' . $directories[$count_1 - 1];
        $sub_directories = Storage::directories($sub_directories_str);
    }

    public function stream()
    {
        if ($this->song == null) {
            throw new Exception("Song is null");
        }

        $path = $this->song->path;
        $file_name = $this->song->title . ".ogg";

        $storage = Storage::disk($this->disk);
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
            throw new Exception("Song is null");
        }
        $this->song->save();
        return $this;
    }

    public function removeFile($filename)
    {
        if (StorageManager::getDisk() == "final_audio") {
            Storage::disk("final_audio")->delete($filename);
        }
        if (StorageManager::getDisk() == "s3") {
            Storage::disk("s3")->delete($filename);
        }
    }
}
