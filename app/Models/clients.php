<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'updated_at',
    ];

    //El cliente pertenece a un usuario (cuenta).
    public function usuari(): BelongsTo
    {
        return $this->belongsTo(Usuaris::class, 'usuari_id');
    }

    // Un cliente puede realizar muchas solicitudes
    public function solicituds(): HasMany
    {
        return $this->hasMany(solicitud::class, 'client_id');
    }

    public function usuaris(): BelongsTo
    {
        return $this->belongsTo(usuaris::class, 'usuari_id');
    }

    public function dni(): BelongsTo
    {
        return $this->belongsTo(dni::class, 'dni_id');
    }
}
