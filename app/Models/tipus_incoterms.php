<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class tipus_incoterms extends Model
{
    use HasFactory;

    protected $table = 'Incoterm'; // La tabla real en BD
    protected $primaryKey = 'id';

    // Quitamos created_at y updated_at del fillable, Laravel los gestiona solos
    protected $fillable = [
        'name',
        'incoterm_type_id',
        'tracking_step_id'
    ];

    public function solicitud(): HasMany
    {
        return $this->hasMany(solicitud::class, 'incoterm_id');
    }

    public function trackingStep()
    {
        return $this->belongsTo(tracking_steps::class, 'tracking_step_id');
    }
}