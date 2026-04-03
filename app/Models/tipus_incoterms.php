<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tipus_incoterms extends Model
{
    use HasFactory;

    protected $table = 'tipus_incoterms';
    protected $fillable = [
        'codi',
        'nom',
        'created_at',
        'updated_at'
    ];

    public function solicitud()
    {
        return $this->hasMany(solicitud::class);
    }
}