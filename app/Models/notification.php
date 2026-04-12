<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class notification extends Model
{
    protected $table = 'notification';
    protected $primaryKey = 'id';
    protected $fillable = [
        'incoterm_id',
        'solicitud_id',
        'date_update',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function incoterm()
    {
        return $this->belongsTo(tipus_incoterms::class, 'incoterm_id');
    }
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'solicitud_id');
    }
}