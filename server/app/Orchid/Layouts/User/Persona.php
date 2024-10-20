<?php

namespace App\Orchid\Layouts\User;

use Illuminate\View\View;
use Orchid\Screen\Contracts\Personable;
use Orchid\Screen\Layouts\Content;

class Persona extends Content
{
    protected $template = 'platform::layouts.persona';

    public function render(Personable $user): View
    {
//        dd($user->profile_photo_url);
        return view($this->template, [
            'title'    => $user->title(),
            'subTitle' => $user->subTitle(),
            'image'    => $user->profile_photo_url ? \Storage::disk('profile_image')->url($user->profile_photo_url) : $user->image(),
            'url'      => $user->url(),
        ]);
    }
}
