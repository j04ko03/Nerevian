<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class notification extends Model
{
    protected $table = 'notification';
    public $timestamps = false;
    protected $fillable = [
        'incoterm_id',
        'solicitud_id',
        'date_update',
    ];

    public function incoterm()
    {
        return $this->belongsTo(tipus_incoterms::class, 'incoterm_id');
    }
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'solicitud_id');
    }
}
