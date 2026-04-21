<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class estats_solicituds extends Model
{
    public $timestamps = false;
    protected $table = 'estats_solicituds';
    protected $primaryKey = 'id';
    protected $fillable = ['estat'];

    public function solicitud(): HasMany
    {
        // Forzamos la FK tal y como la tienes en la vista Vue
        return $this->hasMany(solicitud::class, 'estats_solicitud_id');
    }
}