<?php

namespace App\Orchid\Layouts\Song\Song;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Genre;
use App\Models\Song;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Radio;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class EditSongDetailLayout extends Rows
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
        $song = Song::find($this->query->get('id'));
        return [
            Input::make('song_id')
                ->readonly()
                ->title('Song ID')
                ->value($song?->song_id ?? '')
                ->canSee((bool)$song),
            Input::make('title')
                ->title('Title')
                ->value($song?->title ?? ''),
            Select::make('album_id')
                ->title('Album')
                ->fromModel(Album::class, 'name', 'album_id')
                ->options(array_merge(['' => 'None'], Album::all()->pluck('name', 'album_id')->toArray()))
                ->value($song?->album_id ?? ''),
            Select::make('artist_id')
                ->title('Artists')
                ->fromModel(Artist::class, 'name', 'artist_id')
                ->multiple()
                ->value($song?->artist->pluck('artist_id') ?? [])
                ->help('Required if display is set to public'),
            Select::make('genre_id')
                ->title('Genres')
                ->fromModel(Genre::class, 'name', 'genre_id')
                ->multiple()
                ->value($song?->genre->pluck('genre_id') ?? [])
                ->help('Required if display is set to public'),
            Group::make([
                Radio::make('display')
                    ->placeholder('Public')
                    ->value('public')
                    ->title('Display')
                    ->checked($song?->display == 'public')
                    ->required(),
                Radio::make('display')
                    ->placeholder('Private')
                    ->value('private')
                    ->checked($song?->display == 'private'),
            ])->alignEnd(),

        ];
    }
}
