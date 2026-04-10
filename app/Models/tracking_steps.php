<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tracking_steps extends Model
{

    public $timestamps = false;
    protected $table = 'tracking_steps';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name'
    ];

    public function solicitud()
    {
        return $this->hasMany(solicitud::class);
    }
}
