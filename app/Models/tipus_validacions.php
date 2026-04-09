<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tipus_validacions extends Model
{
    protected $table = 'tipus_validacions';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tipus'
    ];

    public function solicitud()
    {
        return $this->hasMany(solicitud::class);
    }
}
