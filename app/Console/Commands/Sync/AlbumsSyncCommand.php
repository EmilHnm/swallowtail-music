<?php

namespace App\Console\Commands\Sync;

use App\Models\Song;
use App\Models\User;
use App\Models\Album;
use Illuminate\Console\Command;

class AlbumsSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:albums {from?} {to?} {--domain=} {--token=} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Album';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $domain = $this->option('domain') ?? env('SOURCE_DOMAIN');
        $token = $this->option('token') ?? env('SOURCE_TOKEN');
        $from = $this->argument('from') ?? 1000000000;
        $to = $this->argument('to') ?? '';

        $end_point = $domain . '/api/albums?from=' . $from . '&limit=' . $to;

        $this->info('Start sync albums from ' . $end_point);

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
            foreach ($data['albums'] as $raw_album) {
                $this->info('Syncing album ' . $raw_album['id'] . ' - ' . $raw_album['title']);
                $album = new Album();

                if (Album::where('id', $raw_album['id'])->exists()) {
                    $album = Album::where('id', $raw_album['id'])->first();
                }

                $album->id = $raw_album['id'];
                $album->album_id = "album_{$raw_album['browse_id']}";
                $album->user_id = User::first()->user_id;
                $album->name = $raw_album['title'];
                $album->release_year = $raw_album['release'];

                $path = file_path_helper($raw_album['id']) . '/cover.jpg';

                // $full_path = 'storage/upload/album_cover' . $path;

                if (!file_exists(public_path('storage/upload/album_cover/' . file_path_helper($raw_album['id'])))) {
                    mkdir(public_path('storage/upload/album_cover/' . file_path_helper($raw_album['id'])), 0777, true);
                }

                if ($raw_album['thumbnail'] != null) {
                    try {
                        $client->get($raw_album['thumbnail'], ['sink' => public_path('storage/upload/album_cover/' . file_path_helper($raw_album['id']) . '/thumbnail.jpg')]);
                        $album->image_path = file_path_helper($raw_album['id']) . '/thumbnail.jpg';
                    } catch (\Exception $th) {
                        $this->error($raw_album['id'] . " - thumbnail url error");
                        $this->warn($th->getMessage());
                    }
                }


                $album->save();

                foreach ($raw_album['songs'] as $song) {
                    Song::unguard();
                    $song = Song::updateOrCreate([
                        'song_id' => "song_{$song['browse_id']}"
                    ], [
                        'user_id' => User::first()->user_id,
                        'album_id' => $album->album_id,
                        'title' => $song['title'],
                    ]);

                    Song::reguard();
                }
            }
        }
    }
}
