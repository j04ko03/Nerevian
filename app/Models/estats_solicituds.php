<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class estats_solicituds extends Model
{
    protected $table = 'estats_solicituds';
    protected $primaryKey = 'id';
    protected $fillable = [
        'estat',
    ];
    public $timestamps = false;

    public function solicitud(): HasMany
    {
        return $this->hasMany(solicitud::class, 'estat_solicitud_id');
    }
}
