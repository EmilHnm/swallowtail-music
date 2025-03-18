<?php

namespace App\Orchid\Layouts\Genre;

use App\Models\Genre;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class GroupGenreLayout extends Rows
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
        $genre = Genre::find($this->query->get('id'));
        return [
            Input::make('from')
                ->readonly()
                ->value($genre?->genre_id ?? '')
                ->placeholder('From')->hidden(),
            Input::make('')
                ->title('From')
                ->disabled()
                ->value($genre?->name ?? '')
                ->placeholder('From'),
            Select::make('to')
                ->title('To')
                ->options(Genre::where('genre_id', '!=', $genre?->genre_id ?? '')->get()->pluck('name', 'genre_id')->toArray())
                ->required()
                ->placeholder('To'),
        ];
    }
}
