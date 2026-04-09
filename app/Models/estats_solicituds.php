<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class estats_solicituds extends Model
{
    use HasFactory;
    protected $table = 'estats_solicituds';
    protected $primaryKey = 'id';
    protected $fillable = [
        'estat',
    ];
    public $timestamps = false;

    public function solicitud()
    {
        return $this->hasMany(solicitud::class);
    }
}
