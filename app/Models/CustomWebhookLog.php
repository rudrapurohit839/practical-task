<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomWebhookLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'payload',
    ];

    // Optional: If you're saving JSON and want to use it as array
    protected $casts = [
        'payload' => 'array',
    ];
}
