<?php

namespace App\Jobs;

use App\Models\Song;
use App\Services\SongManager;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ProcessSongConvert implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private Song $song, private string $disk, private string $name_final)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        try {
            $song_mananger = new SongManager($this->song);
            $song_mananger->convert($this->name_final);
            $song_mananger->save();
            Storage::disk($this->disk)->delete($this->name_final);
            Storage::disk($this->disk)->delete("chunks/" . $this->song->id);
        } catch (\Exception $ex) {
            dump($ex->getMessage());
            $this->fail($ex->getMessage());
        }
    }
}
