<?php

namespace App\Orchid\Layouts\Genre;

use App\Models\Genre;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Rows;

class EditGenreLayout extends Rows
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
            Input::make('genre_id')
                ->canSee((bool)$genre)
                ->title('Id')
                ->placeholder('Id')
                ->required()
                ->value($genre?->genre_id ?? '')
                ->readonly(),
            Input::make('name')
                ->title('Name')
                ->placeholder('Name')
                ->required()
                ->value($genre?->name ?? ''),
            TextArea::make('description')
                ->title('Description')
                ->value($genre?->description ?? '')
                ->rows(20),
        ];
    }
}
