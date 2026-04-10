<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class estats_operacions extends Model
{
    protected $table = 'estats_operacions';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = ['estat'];
}
