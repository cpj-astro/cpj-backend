<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstrologyData extends Model
{
    use HasFactory;

    protected $table = "astrology_data";

    protected $fillable = [
        'pandit_id', 'match_id', 'date', 'aries', 'taurus', 'gemini', 'cancer', 'leo', 'virgo', 'libra', 'scorpio', 'sagittarius', 'capricorn', 'aquarius', 'pisces'
    ];

    public function pandit()
    {
        return $this->hasOne(Pandits::class, 'id', 'pandit_id');
    }

    public function match()
    {
        return $this->hasOne(Matches::class, 'id', 'match_id');
    }
}
