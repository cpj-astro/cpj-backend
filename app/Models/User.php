<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email_verified_at',
        'birth_date',
        'birth_time',
        'birth_place',
        'latitude',
        'longitude',
        'email',
        'password',
        'user_type',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function createToken(string $name, $abilities = ['*'], $expires_at = null)
    {
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = bin2hex(random_bytes(12))),
            'abilities' => $abilities,
            'expires_at' => $expires_at // Pass the carbon not the integer value
        ]);

        return $plainTextToken;
    }

    public function kundli() {
        return $this->hasOne(Kundli::class, 'user_id');
    }

    public function apiRequest(){
        return $this->hasMany(UserApiRequest::class, 'user_id');
    }
}
