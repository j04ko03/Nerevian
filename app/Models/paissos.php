<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class paissos extends Model
{
    use HasFactory;

    protected $table = 'paissos';

    protected $fillable = [
        'nom',
    ];

    public function ciutats()
    {
        return $this->hasMany(Ciutat::class, 'pais_id');
    }
}