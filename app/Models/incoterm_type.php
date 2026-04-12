<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class incoterm_type extends Model
{
    protected $table = 'Incoterm_type';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function incoterm()
    {
        return $this->hasMany(incoterm::class, 'incoterm_type_id');
    }
}