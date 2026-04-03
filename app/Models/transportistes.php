<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class transportistes extends Model
{
    protected $table = 'transportistes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nom',
        'ciutat_id'
    ];

    public function ciutat()
    {
        return $this->belongsTo(ciutats::class);
    }
}
