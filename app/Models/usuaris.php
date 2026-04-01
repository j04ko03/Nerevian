<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class usuaris extends Authenticatable
{

    use HasApiTokens;
    //
    protected $table = 'usuaris';
    protected $primaryKey = 'id';
    protected $autoIncrement = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = ['correu', 'contrasenya', 'nom', 'cognoms', 'telefon', 'rol_id'];

    protected $hidden = ['contrasenya', 'token'];

    public function peticions_registre(): HasMany
    {
        return $this->hasMany(peticions_registre::class, 'resolt_per');
    }
    public function clients(): HasOne
    {
        return $this->hasOne(clients::class, 'usuari_id');
    }

    public function solicitud(): HasMany
    {
        return $this->hasMany(solicitud::class, 'operador_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(documents::class, 'pujat_per');
    }

    public function operacions(): HasMany
    {
        return $this->hasMany(operacions::class, 'operador_id');
    }

    public function rols(): BelongsTo
    {
        return $this->belongsTo(rols::class, 'rol_id');
    }
}
