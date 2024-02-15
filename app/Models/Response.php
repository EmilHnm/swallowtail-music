<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;

    protected $fillable = [
        'responder',
        'content',
    ];

    public function responder()
    {
        return $this->belongsTo(User::class, "responder", "user_id");
    }
}
