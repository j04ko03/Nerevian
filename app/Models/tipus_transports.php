<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class tipus_transports extends Model
{
    public $timestamps = false; // Corregido: public en lugar de protected
    protected $table = 'tipus_transports';
    protected $primaryKey = 'id';
    protected $fillable = ['nom']; // Cambiado a 'nom' porque tu Vue lee t.nom

    public function solicitud(): HasMany
    {
        // Forzamos la FK en singular para que coincida con tu SQL
        return $this->hasMany(solicitud::class, 'tipus_transport_id');
    }
}