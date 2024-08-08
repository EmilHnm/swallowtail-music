<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use App\Orchid\Screens\Traits\HasDumpModelModal;
use Orchid\Platform\Models\User;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class UserListLayout extends Table
{
    use  HasDumpModelModal;
    /**
     * @var string
     */
    public $target = 'users';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [

            TD::make('id', __('ID'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
            ->render(function (User $user) {
                $id = $user->id;
                $user_id = \Str::limit($user->user_id, 8);
                return $id . "<br>" . $this->getDumpModelToggle(\App\Models\User::class, $id, $user_id);
            }),

            TD::make('name', __('Name'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn (User $user) => new \App\Orchid\Layouts\User\Persona($user->presenter())),

            TD::make('gender', __('Gender'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('email', __('Email'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn (User $user) => ModalToggle::make($user->email)
                    ->modal('asyncEditUserModal')
                    ->modalTitle($user->presenter()->title())
                    ->method('saveUser')
                    ->asyncParameters([
                        'user' => $user->id,
                    ])),

            TD::make('created_at', __('Created'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->defaultHidden()
                ->sort(),

            TD::make('updated_at', __('Last edit'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->sort(),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (User $user) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([

                        Link::make(__('Edit'))
                            ->route('platform.systems.users.edit', $user->id)
                            ->icon('bs.pencil'),

                        ModalToggle::make(__('Send System Notification'))
                            ->icon('bs.bell')
                            ->method('sendNotification')
                            ->modal('asyncSendNotificationModal')
                            ->async('asyncGetUser')
                            ->asyncParameters([
                                'user' => $user->id,
                            ]),

                        Button::make(__('Impersonate user'))
                            ->icon('bg.box-arrow-in-right')
                            ->confirm(__('You can revert to your original state by logging out.'))
                            ->method('loginAs')
                            ->canSee($user->exists && $user->id !== \request()->user()->id),

                        Button::make(__('Disable'))
                            ->icon('bs.ban')
                            ->confirm(__('Are you sure you want to disable this user?'))
                            ->method('remove', [
                                'id' => $user->id,
                            ]),
                    ])),
        ];
    }
}
