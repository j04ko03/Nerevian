<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class tipus_carrega extends Model
{

    public $timestamps = false;
    protected $table = 'tipus_carrega';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tipus'
    ];

    public function solicitud(): HasMany
    {
        return $this->hasMany(solicitud::class, 'tipus_carrega_id');
    }
}
