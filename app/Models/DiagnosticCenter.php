<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class DiagnosticCenter extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function deviceTokens()
    {
        return $this->morphMany(DeviceToken::class, 'tokenable');
    }
} 