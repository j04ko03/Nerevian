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
        'added_by'
    ];

  
    public function paissos()
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }

    public function transportistes()
    {
        return $this->hasMany(transportistes::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(usuaris::class, 'added_by');
    }
}