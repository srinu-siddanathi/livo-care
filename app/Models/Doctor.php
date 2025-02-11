<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Doctor extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'specialization',
        'mobile',
        'gender',
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