<?php

namespace App\Console\Commands\Sync;

use App\Enum\SongMetadataStatusEnum;
use App\Models\Artist;
use App\Models\Song;
use App\Models\SongMetadata;
use App\Enum\SongReferer;
use App\Enum\RefererEnum;
use App\Orchid\Helpers\Text;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\FFMpeg\FFProbe;
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
        $domain = $this->option('domain') ?? config('crawler.domain');
        $token = $this->option('token') ?? config('crawler.token');
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
            $song = Song::where('id', $id)
                ->whereDoesntHave('file')
                // ->orWhereHas('file', function ($query) {
                //     $query->where('referer', RefererEnum::CRAWLER);
                // })
                ->first();

            if ($song) {
                $this->info("Syncing song " . $song->id . " - " . $song->title);
                try {

                    $response = $client->get($end_point . str_replace('song_', '', $song->song_id));

                    $data = json_decode($response->getBody()->getContents(), true);
                    if (isset($data['status']) && $data['status'] == 'success') {
                        $raw_data = $data['song'];
                        $song->title = $raw_data['title'];
                        $song->normalized_title = Text::normalize($raw_data['title']);
                        $ffdisk = FFMpeg::fromDisk($raw_data['storage_type'])->open($raw_data['storage']);
                        $song->display = 'public';
                        $size = Storage::disk($raw_data['storage_type'])->size($raw_data['storage']);
                        $hash = hash('md5', Storage::disk($raw_data['storage_type'])->get($raw_data['storage']));
                        SongMetadata::unguard();
                        $file = SongMetadata::updateOrCreate([
                            'song_id' => $song->song_id
                        ], [
                            'file_path' => $raw_data['storage'],
                            'driver' => $raw_data['storage_type'],
                            'lyrics' => $raw_data['lyric']['lyrics'],
                            'size' =>   $size,
                            'hash' => $hash,
                            'status' => SongMetadataStatusEnum::DONE,
                            'referer' => RefererEnum::CRAWLER,
                            'duration' => $ffdisk->getDurationInSeconds(),
                        ]);

                        $song->artist()->detach();

                        foreach ($raw_data['artists'] as $artist) {
                            $artist = Artist::where('artist_id', 'artist_' . $artist['channel_id'])
                                ->orWhere('id', $artist['id'])->firstOrFail();
                            if ($artist) {
                                $song->artist()->attach($artist->artist_id);
                                $this->warn("   Syncing with artist " . $artist->name);
                            }
                        }

                        $song->genre()->detach();
                        foreach ($raw_data['genres'] as $genre) {
                            $genre_data = \App\Models\Genre::where('genre_id', 'genre_' . $genre['id'])
                                ->orWhere('id', $genre['id'])->firstOrFail();
                            if ($genre_data) {
                                $song->genre()->attach($genre_data->genre_id);
                                $this->warn("   Syncing with genre " . $genre_data->name);
                            }
                        }

                        SongMetadata::reguard();
                        $song->save();

                        $song->searchable();
                    }
                } catch (\Exception $e) {
                    \Log::error("Error syncing song " . $song->id . " - " . $song->title . " with errors " . $e);
                    continue;
                }
                sleep(0);
            } else {
                $this->warn("Song not found with id " . $id);
            }
        }
    }
}
