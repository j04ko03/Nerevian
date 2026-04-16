<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class tipus_fluxes extends Model
{
    public $timestamps = false;
    protected $table = 'tipus_fluxes';
    protected $primaryKey = 'id';
    protected $fillable = ['nom'];

    public function solicitud(): HasMany
    {
        return $this->hasMany(solicitud::class, 'tipus_fluxe_id');
    }
}