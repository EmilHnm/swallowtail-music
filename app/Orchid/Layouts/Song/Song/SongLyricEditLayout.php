<?php

namespace App\Orchid\Layouts\Song\Song;

use App\Models\SongMetadata;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Rows;

class SongLyricEditLayout extends Rows
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    /**
     * Get the fields elements to be displayed.
     *
     * @return Field[]
     */
    protected function fields(): iterable
    {
        $lyric = '';
        $raw_lyric = SongMetadata::find($this->query->get('id'));
        if ($raw_lyric) {
            $lyric = json_decode($raw_lyric->lyrics)->lyric ?? [];
            $lyric = implode("\n", $lyric);
        }
        return [
            TextArea::make('lyric')
                ->title('Lyric')
                ->value($lyric)
                ->rows(20),
        ];
    }
}
