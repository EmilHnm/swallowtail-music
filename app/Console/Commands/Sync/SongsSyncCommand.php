<?php

namespace App\Console\Commands\Sync;

use FFMpeg\FFProbe;
use App\Models\Artist;
use App\Models\SongFile;
use Illuminate\Console\Command;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class SongsSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:songs {from?} {to?} {--domain=} {--token=} {--force}';

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
        $domain = $this->option('domain') ?? env('SOURCE_DOMAIN');
        $token = $this->option('token') ?? env('SOURCE_TOKEN');
        $from = $this->argument('from') ?? 1;
        $to = $this->argument('to') ?? $from + 99;


        $end_point = $domain . '/api/song/';

        $client = new \GuzzleHttp\Client([
            'timeout' => 60,
            'connect_timeout' => 60,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ]
        ]);
        foreach (range($from, $to) as $id) {
            $song = \App\Models\Song::where('id', $id)->first();
            if ($song) {
                $this->info('Syncing song ' . $song->id . ' - ' . $song->title);

                $response = $client->get($end_point . str_replace('song_', '', $song->song_id));

                $data = json_decode($response->getBody()->getContents(), true);

                if (isset($data['status']) && $data['status'] == 'success') {
                    $raw_data = $data['song'];
                    // dd($raw_data);
                    $song->title = $raw_data['title'];
                    $song->duration = FFMpeg::fromDisk($raw_data['storage_type'])
                        ->open($raw_data['storage'])
                        ->getDurationInSeconds();
                    $song->listens = 0;
                    $song->display = 'public';
                    SongFile::unguard();
                    $file = SongFile::updateOrCreate([
                        'song_id' => $song->song_id
                    ], [
                        'file_path' => $raw_data['storage'],
                        'driver' => $raw_data['storage_type'],
                        'lyrics' => $raw_data['lyric']['lyrics'],
                        'status' => 'done'
                    ]);

                    $song->artist()->detach();

                    foreach ($raw_data['artists'] as $artist) {
                        $artist = Artist::where('artist_id', 'artist_' . $artist['channel_id'])
                            ->orWhere('id', $artist['id'])->firstOrFail();
                        if ($artist) {
                            $song->artist()->attach($artist->artist_id);
                        }
                    }

                    SongFile::reguard();
                    $song->save();
                    // dd();
                }
            }
            sleep(5);
        }
    }
}
