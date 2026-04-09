<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Pais;

class ciutats extends Model
{
    use HasFactory;

    protected $table = 'ciutats';

    protected $fillable = [
        'nom',
        'pais_id',
    ];

  
    public function paissos()
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }

    public function transportistes()
    {
        return $this->hasMany(transportistes::class);
    }
}