<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StripeTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'currency',
        'payment_status',
    ];
}
