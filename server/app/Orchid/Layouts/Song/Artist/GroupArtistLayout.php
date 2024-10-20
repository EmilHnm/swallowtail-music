<?php

namespace App\Orchid\Layouts\Song\Artist;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class GroupArtistLayout extends Rows
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
        $start_id = $this->query->get('id');
        return [
            Input::make('start_id')
                ->title('Artist Group From\'s ID')
                ->required()
                ->value($this->query->get('id'))
                ->readonly()
                ->help('ID of the Artist to be grouped'),
            Input::make('endpoint_id')
                ->title('Artist Group To\'s ID')
                ->required()
                ->help('ID of the Artist the selected Artist will be grouped to'),
        ];

    }
}
