<?php

namespace App\Models;

use App\Casts\JSON;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $casts = [
        'body' => JSON::class,
    ];
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester', 'user_id');
    }
}
