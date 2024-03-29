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
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ProcessSongConvert implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

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
        $this->queueProgress(0);
        try {
            $song_mananger = new SongManager($this->song);
            $this->queueProgress(10);
            $song_mananger->convert($this->name_final);
            $this->queueProgress(90);
            $song_mananger->save();
            $this->queueProgress(95);
            Storage::disk($this->disk)->delete($this->name_final);
            $this->queueProgress(100);
        } catch (\Exception $ex) {
            dump($ex->getMessage());
            $this->fail($ex->getMessage());
        }
    }
}
