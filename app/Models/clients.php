<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\usuari;
use App\Models\dni;
use App\Models\solicitud;

class clients extends Model
{
    protected $table = 'clients';
    protected $primaryKey = 'id';
    protected $fillable = [
        'usuari_id',
        'dni_id',
        'created_at',
        'updated_at'
    ];

    public function solicitud()
    {
        return $this->hasMany(solicitud::class);
    }

    public function usuari()
    {
        return $this->belongsTo(usuari::class);
    }

    public function dni()
    {
        return $this->belongsTo(dni::class);
    }
}
