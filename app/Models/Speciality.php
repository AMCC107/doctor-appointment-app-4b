<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @deprecated Use Specialty (tabla specialties) para nuevo código.
 */
class Speciality extends Model
{
    protected $table = 'specialties';

    protected $fillable = [
        'name',
    ];

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }
}