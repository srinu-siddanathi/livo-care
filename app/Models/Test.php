<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'volume',
        'price'
    ];

    protected $casts = [
        'price' => 'decimal:2'
    ];

    public function prescriptions()
    {
        return $this->belongsToMany(Prescription::class);
    }
} 