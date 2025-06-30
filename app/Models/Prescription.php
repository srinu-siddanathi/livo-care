<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'patient_name',
        'patient_mobile',
        'patient_age',
        'patient_gender',
        'notes',
        'status',
        'image_url'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function tests()
    {
        return $this->belongsToMany(Test::class);
    }
} 