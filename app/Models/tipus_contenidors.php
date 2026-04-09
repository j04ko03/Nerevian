<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tipus_contenidors extends Model
{
    protected $table = 'tipus_contenidors';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tipus'
    ];

    public function solicitud()
    {
        return $this->hasMany(solicitud::class);
    }
}
