<?php

namespace App\Orchid\Layouts\Song\Album;

use App\Models\Album;
use App\Models\Song;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class EditAlbumSongsLayout extends Rows
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
        $album = Album::find($this->query->get('id'));
        return [
            Input::make('id')
                ->readonly()
                ->canSee((bool)$album)
                ->required()
                ->value($album?->album_id),
            Matrix::make('songs')
                ->columns([
                    'Title' => 'song_id',
                ])->fields([
                    'song_id' => Select::make()
                        ->fromQuery(Song::query()->whereNull('album_id')
                            ->orWhere('album_id', $album?->album_id),
                            'title'),
                ])->value($album?->song ?? [])
                ->title('Songs List')
                ->help('Songs in this album'),
        ];
    }
}
