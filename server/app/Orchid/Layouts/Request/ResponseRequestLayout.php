<?php

namespace App\Orchid\Layouts\Request;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Rows;
use Orchid\Support\Facades\Layout;

class ResponseRequestLayout extends Rows
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
        return [
            Input::make('id')->title('ID')->readonly()->value($this->query->get('id'))->required(),
            TextArea::make('content')->title('Content')->required()->rows(10),
        ];
    }
}
