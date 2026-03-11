<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    protected $fillable = [
        'appointment_id',
        'medication',
        'dose',
        'frequency_duration',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
