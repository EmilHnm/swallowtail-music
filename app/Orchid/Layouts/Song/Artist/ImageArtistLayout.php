<?php

namespace App\Orchid\Layouts\Song\Artist;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class ImageArtistLayout extends Rows
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
            Input::make('id')
                ->title('ID')
                ->value(fn() => $this->query->get('id'))
                ->readonly(),
            Select::make('via')
                ->title('Via')
                ->options([
                    'url' => 'URL',
                    'file' => 'File'
                ]),
            Input::make('url')
                ->title('URL')
                ->help('URL of the image to be downloaded, only works if Via is set to URL'),
            Input::make('file')
                ->title('File')
                ->type('file')
                ->accept('image/*')
                ->help('File of the image to be uploaded, only works if Via is set to File'),
        ];
    }
}
