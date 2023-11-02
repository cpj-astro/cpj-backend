<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Players extends Model
{
    use HasFactory;

    protected $table = "players"; 

    protected $fillable = [
        'name','avatar_image','date','month','year','hours','minutes','seconds','latitude','longitude','timezone','birthplace','height','role','batting_style','bowling_style', 'current_age'
    ];
    
    public function kundli()
    {
        return $this->hasOne(Kundli::class, 'player_id');
    }
}
