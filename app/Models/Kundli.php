<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kundli extends Model
{
    use HasFactory;

    protected $table = "kundli"; 

    protected $fillable = [
        'user_id','player_id','match_id','kundli_data'
    ];
}
