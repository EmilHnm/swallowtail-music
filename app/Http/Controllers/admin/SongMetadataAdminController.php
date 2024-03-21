<?php
namespace App\Http\Controllers\admin;

use App\Enum\SongMetadataStatusEnum;
use App\Models\Song;
use App\Models\SongMetadata;
use app\Services\SongManager;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Toast;

trait SongMetadataAdminController
{

    public function asyncPassingId(Request $request)
    {
        return [
            'id' => $request->get('id'),
        ];
    }
    public function saveLyrics(Request $request)
    {
        $id = $request->get('id');
        $lyric = $request->get('lyric');

        try {
            $songMetadata = SongMetadata::find($id);
            $songMetadata->lyrics = json_encode(['lyric' => explode("\n", $lyric)]);
            $songMetadata->save();
        } catch (\Exception $e) {
            Toast::error($e->getMessage());
            return redirect()->back();
        }

        Toast::info('Lyrics saved');
        return redirect()->route('platform.app.song-metadata');
    }

    public function download($id)
    {
        try {
            $metadata = SongMetadata::findOrFail($id);
            $file = $metadata->file_path;
            $url = (new SongManager(Song::find($metadata->song_id)))->generateDownloadUrl();
            return redirect()->to($url);
        } catch (\Exception $e) {
            Toast::error($e->getMessage());
            return redirect()->back();
        }
    }

    public function publish($id)
    {
        try {
            $metadata = SongMetadata::findOrFail($id);
            if(!in_array($metadata->status, SongMetadataStatusEnum::notReadyStatus())) {
                $metadata->status = SongMetadataStatusEnum::PUBLISH;
                $metadata->save();
                Toast::info('Metadata published');
            } else {
                Toast::error('Metadata is not ready to be published');
            }
        } catch (\Exception $e) {
            Toast::error($e->getMessage());
        }
        return redirect()->back();
    }

    public function setErrors(Request $request) {
        try {
            $metadata = SongMetadata::findOrFail($request->id);
            $metadata->status = $request->status;
            $metadata->save();
            Toast::info('Metadata status updated');
        } catch (\Exception $e) {
            Toast::error($e->getMessage());
        }
    }
}
