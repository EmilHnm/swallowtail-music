<?php

namespace App\Models;

use App\Casts\JSON;
use App\Models\Traits\AdvancedFilters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Request extends Model
{
    use HasFactory, AsSource, AdvancedFilters;

    protected $casts = [
        'body' => JSON::class,
    ];
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester', 'user_id');
    }
    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    public function filledBy()
    {
        return $this->belongsTo(User::class, 'filled_by', 'user_id');
    }
}
