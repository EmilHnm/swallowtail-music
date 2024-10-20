<?php

namespace App\Orchid\Layouts\Song\Artist;

use App\Models\Artist;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Rows;

class EditArtistDetailLayout extends Rows
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
        $artist = Artist::find($this->query->get('id'));
        return [
            Input::make('id')
                ->title('ID')
                ->value(fn() => $artist?->id ?? '')
                ->canSee((bool)$artist)
                ->readonly(),
            Input::make('name')
                ->title('Name')
                ->value(fn() => $artist?->name ?? ''),
            TextArea::make('description')
                ->title('Description')
                ->value(fn() => $artist?->description ?? '')
                ->rows(10),
        ];
    }
}
