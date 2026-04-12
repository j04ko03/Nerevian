<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class paissos extends Model
{
    use HasFactory;

    protected $table = 'paissos';

    protected $fillable = [
        'nom',
        'added_by',
    ];

    public function ciutats(): HasMany
    {
        return $this->hasMany(ciutats::class, 'pais_id');
    }

    public function addedBy()
    {
        return $this->belongsTo(usuaris::class, 'added_by');
    }
}