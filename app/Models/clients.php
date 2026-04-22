<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class clients extends Model
{
    protected $table = 'clients';
    protected $primaryKey = 'id';
    protected $fillable = [
        'usuari_id',
        'dni_id',
        'actiu',
    ];

    protected $casts = [
        'actiu' => 'boolean',
    ];

    // clients.usuari_id → usuaris.id
    public function usuaris(): BelongsTo
    {
        return $this->belongsTo(usuaris::class, 'usuari_id');
    }

    public function dni(): BelongsTo
    {
        return $this->belongsTo(dni::class, 'dni_id');
    }

    public function solicituds(): HasMany
    {
        return $this->hasMany(solicitud::class, 'client_id');
    }
}