<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class incoterm extends Model
{
    protected $table = 'Incoterm';

    protected $fillable = [
        'name',
        'incoterm_type_id',
        'tracking_step_id',
        'added_by',
    ];

    protected $dateFormat = 'Y-m-d';

    public function incotermType()
    {
        return $this->belongsTo(incoterm_type::class, 'incoterm_type_id');
    }

    public function trackingStep()
    {
        return $this->belongsTo(tracking_steps::class, 'tracking_step_id');
    }

    public function addedBy()
    {
        return $this->belongsTo(usuaris::class, 'added_by');
    }
}