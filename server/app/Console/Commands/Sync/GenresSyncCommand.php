<?php

namespace App\Console\Commands\Sync;

use App\Models\Song;
use App\Models\User;
use App\Models\Album;
use App\Models\Genre;
use Illuminate\Console\Command;

class GenresSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:genres {--domain=} {--token=} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Genres';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $domain = $this->option('domain') ?? env('SOURCE_DOMAIN');
        $token = $this->option('token') ?? env('SOURCE_TOKEN');

        $end_point = $domain . '/api/genres';

        $this->info('Start sync genres from ' . $end_point);

        $client = new \GuzzleHttp\Client([
            'timeout' => 60,
            'connect_timeout' => 60,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ]
        ]);

        $response = $client->get($end_point);

        $data = json_decode($response->getBody()->getContents(), true);

        if (isset($data['status']) && $data['status'] == 'success') {
            foreach ($data['genres'] as $genre) {
                Genre::unguard();
                $genre_data = Genre::updateOrCreate(
                    [
                        'genre_id' => 'genre_' . $genre['id'],
                    ],
                    [
                        'name' => $genre['name'],
                        'slug' => $genre['slug'],
                        'description' => $genre['description'],
                        'ref' => $genre['ref'],
                    ]
                );
                Genre::reguard();
                $this->info('Synced genre ' . $genre_data->name);
                $this->warn('==================================');
            }
        }
    }
}
