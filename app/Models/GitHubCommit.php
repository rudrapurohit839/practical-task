<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GitHubCommit extends Model
{
    use HasFactory;

    protected $fillable = [
        'commit_id',
        'message',
        'author',
    ];
}
