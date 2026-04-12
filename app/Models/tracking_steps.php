<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\incoterm;

class tracking_steps extends Model
{
    protected $table = 'tracking_steps';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

   public function incoterms()
   {
       return $this->belongsTo(incoterm::class, 'id_incoterm');
   }
}
