<?php

namespace App\Orchid\Layouts\Song\Album;

use App\Enum\AlbumTypeEnum;
use App\Models\Album;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class EditAlbumDetailLayout extends Rows
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
            Input::make('name')
                ->title('Name')
                ->placeholder('Album name')
                ->required()
                ->value($album?->name),
            Select::make('type')
                ->title('Type')
                ->options(AlbumTypeEnum::toArray())
                ->required()
                ->value($album?->type),
            Input::make('release_year')
                ->title('Release Year')
                ->type('number')
                ->required()
                ->value($album?->release_year),
            Select::make('cover_source')
                ->options([
                    '' => 'None',
                    'url' => 'URL',
                    'file' => 'File',
                ])
                ->title('Cover Source')
                ->required(!$album),
            Input::make('url')
                ->title('URL')
                ->help('URL of the image to be downloaded, only works if Via is set to URL'),
            Input::make('file')
                ->title('File')
                ->type('file')
                ->accept('image/*')
                ->help('File of the image to be uploaded, only works if Via is set to File')
        ];
    }
}
