<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;


class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use HasFactory;

    protected $casts = [
        'abilities' => 'json',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    protected $fillable = [
        'name',
        'token',
        'abilities',
        'expires_at'
    ];
}
