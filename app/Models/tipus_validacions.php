<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class tipus_validacions extends Model
{
    public $timestamps = false;
    protected $table = 'tipus_validacions';
    protected $primaryKey = 'id';
    protected $fillable = ['nom'];

    public function solicitud(): HasMany
    {
        return $this->hasMany(solicitud::class, 'tipus_validacio_id');
    }
}