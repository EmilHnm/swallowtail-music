<?php

namespace App\Console\Commands\Sync;

use App\Orchid\Helpers\Text;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ArtistSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:artists {from?} {to?} {--domain=} {--token=} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync artist from domain';

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

        $end_point = $domain . '/api/artists?from=' . $from . '&limit=' . $to;

        $this->info('Start sync artists from ' . $end_point);

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
            foreach ($data['artists'] as $item) {
                $this->info('Syncing artist ' . $item['id'] . ' - ' . $item['name']);
                $artist = \App\Models\Artist::where('id', $item['id'])->first();
                if (!$artist) {
                    $artist = new \App\Models\Artist();
                    $artist->id = $item['id'];
                }
                $artist->artist_id = $item['channel_id'] ? "artist_" . $item['channel_id'] : "artist_" . \Str::random(10);
                $artist->name = $item['name'];
                $artist->normalized_name = Text::normalize($item['name']);
                $artist->description = $item['description'];

                // dd(Storage::disk('public')->url('/upload/artist_image/1000000005/avatar.jpg'));

                if (!file_exists(public_path('storage/upload/artist_image/' . $item['id']))) {
                    mkdir(public_path('storage/upload/artist_image/' . $item['id']), 0777, true);
                }

                if ($item['avatar'] != null) {
                    try {
                        \Storage::disk('final_artist_image')->put($item['id'] . '/avatar.jpg', file_get_contents($item['avatar']));
//                        $client->get($item['avatar'], ['sink' => public_path('storage/upload/artist_image/' . $item['id'] . '/avatar.jpg')]);
                        $artist->image_path = $item['id'] . '/avatar.jpg';
                    } catch (\Exception $e) {
                        $this->error($item['id'] . " - avatar url error");
                    }
                }

                if ($item['banner'] != null) {
                    try {
                        \Storage::disk('final_artist_image')->put($item['id'] . '/banner.jpg', file_get_contents($item['banner']));
//                        $client->get($item['banner'], ['sink' => public_path('storage/upload/artist_image/' . $item['id'] . '/banner.jpg')]);
                        $artist->banner_path = $item['id'] . '/banner.jpg';
                    } catch (\Exception $th) {
                        $this->error($item['id'] . " - banner url error");
                    }
                }

                $artist->save();
            }
        }
    }
}
