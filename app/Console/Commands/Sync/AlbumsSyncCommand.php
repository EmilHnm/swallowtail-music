<?php

namespace App\Console\Commands\Sync;

use App\Enum\RefererEnum;
use App\Models\Song;
use App\Models\User;
use App\Models\Album;
use App\Orchid\Helpers\Text;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

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
        $domain = $this->option('domain') ?? config('crawler.domain');
        $token = $this->option('token') ?? config('crawler.token');
        $from = $this->argument('from') ?? 1000000000;
        $to = $this->argument('to') ?? '';

        $end_point = $domain . '/api/albums?from=' . $from . '&limit=' . $to;

        if (!User::first()) {
            $this->error('User not found');
            return;
        }

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
                $album->normalized_name = Text::normalize($raw_album['title']);
                $album->type = $raw_album['type'];
                $album->referer = RefererEnum::CRAWLER;
                $album->release_year = $raw_album['release'];

//                $path = file_path_helper($raw_album['id']) . '/cover.jpg';

                // $full_path = 'storage/upload/album_cover' . $path;
//
//                if (!file_exists(public_path('storage/upload/album_cover/' . file_path_helper($raw_album['id'])))) {
//                    mkdir(public_path('storage/upload/album_cover/' . file_path_helper($raw_album['id'])), 0777, true);
//                }


                if ($raw_album['thumbnail'] != null) {
                    try {
                        Storage::disk('final_cover')->put(file_path_helper($raw_album['id']) . '/thumbnail.jpg', $client->get($raw_album['thumbnail'])->getBody()->getContents());
//                        $client->get($raw_album['thumbnail'], ['sink' => public_path('storage/upload/album_cover/' . file_path_helper($raw_album['id']) . '/thumbnail.jpg')]);
                        $album->image_path = file_path_helper($raw_album['id']) . '/thumbnail.jpg';
                    } catch (\Exception $th) {
                        $this->error($raw_album['id'] . " - thumbnail url error");
                        $this->warn($th->getMessage());
                    }
                }

                $album->save();

                $this->warn('   Synchronized album ' . $raw_album['id']);
                $this->warn("       {$album->name}");
                $this->warn("       {$album->release_year}");

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
