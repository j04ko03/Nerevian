<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tipus_carrega extends Model
{
    protected $table = 'tipus_carrega';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tipus'
    ];

    public function solicitud()
    {
        return $this->hasMany(solicitud::class);
    }
}
