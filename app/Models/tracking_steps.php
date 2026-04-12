<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\incoterm;

class tracking_steps extends Model
{
    public $timestamps = false;
    protected $table = 'tracking_steps';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'name'
    ];

   public function incoterms()
   {
       return $this->belongsTo(incoterm::class, 'id_incoterm');
   }

    public function solicitud()
    {
        return $this->hasMany(solicitud::class);
    }
}
