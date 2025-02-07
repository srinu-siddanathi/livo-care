<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Doctor extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'specialization',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
} 