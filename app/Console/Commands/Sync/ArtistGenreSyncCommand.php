<?php

namespace App\Console\Commands\Sync;

use Illuminate\Console\Command;

class ArtistGenreSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:artist-genre-sync-command';

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
        $artists = \App\Models\Artist::doesntHave('genres')->get();
        foreach ($artists as $artist) {
            $genres = $artist->song()->with('genre')->get()
                ->pluck('genre')
                ->flatten()
                ->unique('genre_id')
                ->pluck('genre_id')
                ->toArray();
            $artist->genres()->sync($genres);
            $this->info("Synced artist {$artist->name}");
            foreach ($genres as $genre) {
                $this->info("Synced genre {$genre}");
            }
        }
    }
}
