<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class rols extends Model
{
    //

    public function usuaris(): HasOne
    {
        return $this->hasOne(usuaris::class , 'rol_id');
    }
}
