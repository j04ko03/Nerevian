<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class usuaris extends Model
{
    //
    protected $table = 'usuaris';
    protected $primaryKey = 'id';
    protected $autoIncrement = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = ['correu', 'contrasenya', 'nom', 'cognoms', 'telefon'];

    public function peticions_registre(): HasMany
    {
        return $this->hasMany(peticions_registre::class , 'resolt_per');
    }
    public function clients(): HasOne
    {
        return $this->hasOne(clients::class , 'usuari_id');
    }

    public function solicitud(): HasMany
    {
        return $this->hasMany(solicitud::class , 'operador_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(documents::class , 'pujat_per');
    }

    public function operacions(): HasMany
    {
        return $this->hasMany(operacions::class , 'operador_id');
    }
}
