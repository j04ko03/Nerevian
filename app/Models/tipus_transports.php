<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tipus_transports extends Model
{

    protected $timestamps = false;
    protected $table = 'tipus_transports';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tipus'
    ];

    public function solicitud()
    {
        return $this->hasMany(solicitud::class);
    }
}
