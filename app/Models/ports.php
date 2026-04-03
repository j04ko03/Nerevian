<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ports extends Model
{
    use HasFactory;

    protected $table = 'ports';
    protected $fillable = [
        'nom',
        'ciutat_id',
    ];

    public function ciutats()
    {
        return $this->belongsTo(Ciutat::class, 'ciutat_id');
    }

    public function solicitudOrigen()
    {
        return $this->hasMany(solicitud::class, 'port_origen_id');
    }

    public function solicitudDesti()
    {
        return $this->hasMany(solicitud::class, 'port_desti_id');
    }
}