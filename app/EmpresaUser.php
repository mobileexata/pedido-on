<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmpresaUser extends Model
{
    protected $table = 'empresa_user';
    protected $fillable = [
        'user_id', 'empresa_id'
    ];
}
