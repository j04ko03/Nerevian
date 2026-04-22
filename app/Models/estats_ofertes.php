<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class estats_ofertes extends Model
{
    protected $table = 'estats_ofertes';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'estat',
    ];
}