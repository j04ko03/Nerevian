<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class peticions_registre extends Model
{
    //
    protected $table = 'peticions_registre';
    protected $primaryKey = 'id';
    protected $autoIncrement = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = ['nom_empresa', 'contacte', 'missatge', 'correu', 'contrasenya', 'telefon', 'rol_id'];

    // Creado para que Laravel trate los campos como Carbon (fechas).
    // Carbon trata una columna como objeto fecha y hora. En este caso, si no se especifica la fecha se 
    // debería de poner null o tratarla como string y eso no cuadraría con la base de datos.
    // Por lo tanto, se especifica que data_creacio y data_resolucio son fechas.
    protected $casts = [
        'data_creacio' => 'datetime',
        'data_resolucio' => 'datetime',
    ];

    protected $attributes = [
        'estat' => '0',
        'resolt_per' => null,
        'data_resolucio' => null,
        'rao_rebuig' => null,
    ];

    // Se ejecuta cuando el modelo es inicializado por Laravel.
    protected static function booted()
    {
        static::creating(function ($peticion) {
            // Asigna la fecha actual solo si no se ha definido previamente
            if (is_null($peticion->data_creacio)) {
                $peticion->data_creacio = now();
            }
        });
    }

    public function peticions_registre(): BelongsTo
    {
        return $this->belongsTo(usuaris::class, 'id');
    }

    public function resolutor(): BelongsTo
    {
        return $this->belongsTo(usuaris::class, 'resolt_per');
    }
}
