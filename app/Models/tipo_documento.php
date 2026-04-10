<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tipo_documento extends Model
{
    protected $table = 'tipo_documento';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = ['name'];
}
