<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowedUsernames extends Model
{
    protected $fillable = [
        'username',
        'claimed',
        'role'
    ];
}
