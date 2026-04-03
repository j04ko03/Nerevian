<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tipus_fluxes extends Model
{
    protected $table = 'tipus_fluxes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tipus'
    ];

    public function solicitud()
    {
        return $this->hasMany(solicitud::class);
    }
}
