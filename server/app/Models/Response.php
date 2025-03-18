<?php

namespace App\Models;

use App\Models\Traits\AdvancedFilters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Response extends Model
{
    use HasFactory, AsSource, AdvancedFilters;

    protected $fillable = [
        'responder',
        'content',
    ];

    public function responder()
    {
        return $this->belongsTo(User::class, "responder", "user_id");
    }

    public function request() {
        return $this->belongsTo(Request::class);
    }
}
