<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class dni extends Model
{
    protected $table = 'dni';
    public $timestamps = false; // Lo pongo porque en el SQL no tiene created_at/updated_at

    protected $fillable = ['dni'];

    //Este DNI le pertenece a un cliente específico.
    public function client(): HasOne
    {
        return $this->hasOne(Clients::class, 'dni_id');
    }
}
