<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tipus_incoterms extends Model
{
    use HasFactory;

    protected $table = 'Incoterm';

    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'incoterm_type_id',
        'tracking_step_id',
        'created_at',
        'updated_at',
    ];

    public function solicitud()
    {
        return $this->hasMany(solicitud::class);
    }

    public function trackingStep()
    {
        return $this->belongsTo(tracking_steps::class, 'tracking_step_id');
    }
}