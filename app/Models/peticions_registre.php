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
    protected $fillable = ['nom_empresa', 'contacte', 'missatge', 'correu', 'contrasenya', 'telefon'];
    protected $attributes = [
        'estat' => '0',
        'data_creacio' => now(),
        'resolt_per' => null,
        'data_resolucio' => null,
        'rao_rebuig' => null,
    ];

    public function peticions_registre(): BelongsTo
    {
        return $this->belongsTo(usuaris::class, 'resolt_per');
    }
}
