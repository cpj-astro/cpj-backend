<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pandits extends Model
{
    use HasFactory;
    protected $table = "pandits";

    protected $fillable = [
        'name',
        'avatar_image',
        'description',
        'experience',
        'rating',
        'match_astrology_price'
    ];
}
