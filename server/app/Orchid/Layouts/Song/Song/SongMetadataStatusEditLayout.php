<?php

namespace App\Orchid\Layouts\Song\Song;

use App\Enum\SongMetadataStatusEnum;
use App\Models\SongMetadata;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class SongMetadataStatusEditLayout extends Rows
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
        $selection = [];
        foreach (SongMetadataStatusEnum::toArray() as $status) {
            if (str_contains($status, 'error'))
                $selection[$status] = SongMetadataStatusEnum::getErrorMessage($status);
        }
        return [
            Input::make('id')->value($this->query->get('id'))->readonly(),
            Select::make('status')
                ->options($selection)
                ->title('Status')
                ->help('Select the status of the metadata')
        ];
    }
}
