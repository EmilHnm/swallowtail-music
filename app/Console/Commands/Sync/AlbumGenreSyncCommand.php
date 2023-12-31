<?php

namespace App\Console\Commands\Sync;

use App\Models\Song;
use Illuminate\Console\Command;

class AlbumGenreSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:album-genre-sync-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $albums = \App\Models\Album::doesntHave('genre')->get();
        foreach ($albums as $album) {
            $genres = Song::with('genre')->where('album_id', $album->album_id)->get()->pluck('genre')->flatten()->pluck('genre_id')->unique();
            $album->genre()->sync($genres);
            $this->info("Synced album " . $album->album_id . " with {$genres->count()} genres");
            foreach ($genres as $genre) {
                $this->info("   {$genre}");
            }
        }
    }
}
