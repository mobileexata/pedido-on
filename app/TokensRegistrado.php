<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TokensRegistrado extends Model
{
    protected $fillable = [
        'user_id', 'token'
    ];
}
