<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Charge extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'montant',
    ];
    protected static function booted()
    {
        static::creating(function ($charge) {
            $charge->mois = now()->month;
            $charge->annee = now()->year;
        });
    }
}
