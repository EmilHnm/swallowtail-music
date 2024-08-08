<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class UserEditLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Input::make('user.user_id')
                ->type('text')
                ->required()
                ->title(__('User ID'))
                ->readonly()
                ->value(function ($user_id) {
                    if(gettype($user_id) == 'string')
                        return $user_id ?: \Str::uuid();
                    else
                        return  \Str::uuid();
                }),

            Input::make('user.name')
                ->type('text')
                ->max(255)
                ->required()
                ->title(__('Name'))
                ->placeholder(__('Name')),

            Input::make('user.email')
                ->type('email')
                ->required()
                ->title(__('Email'))
                ->placeholder(__('Email')),

        ];
    }
}
