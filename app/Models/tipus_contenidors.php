<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class tipus_contenidors extends Model
{
    public $timestamps = false;
    protected $table = 'tipus_contenidors';
    protected $primaryKey = 'id';
    protected $fillable = ['nom'];

    public function solicitud(): HasMany
    {
        return $this->hasMany(solicitud::class, 'tipus_contenidor_id');
    }
}