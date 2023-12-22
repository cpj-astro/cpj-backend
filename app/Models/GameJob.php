<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameJob extends Model
{
    use HasFactory;

    protected $table = "game_job"; 

    protected $fillable = [
        'game_link','status'
    ];
}
