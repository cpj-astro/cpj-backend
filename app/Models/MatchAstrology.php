<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchAstrology extends Model
{
    use HasFactory;

    protected $table = "match_astrology";

    protected $fillable = [
        'id',
        'user_id',
        'match_id',
        'astrology_data',
        'created_at',
        'updated_at'
    ];
}
