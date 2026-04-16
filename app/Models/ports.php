<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ports extends Model
{
    use HasFactory;

    protected $table = 'ports';
    protected $fillable = [
        'nom',
        'ciutat_id',
        'added_by'
    ];

    public function ciutat()
    {
        return $this->belongsTo(ciutats::class, 'ciutat_id');
    }

    public function solicitudOrigen(): HasMany
    {
        return $this->hasMany(solicitud::class, 'port_origen_id');
    }

    public function solicitudDesti(): HasMany
    {
        return $this->hasMany(solicitud::class, 'port_desti_id');
    }

    public function addedBy()
    {
        return $this->belongsTo(usuaris::class, 'added_by');
    }
}