<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function prescriptions()
    {
        return $this->belongsToMany(Prescription::class);
    }
} 