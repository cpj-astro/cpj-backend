<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CupRateTeams extends Model
{
    use HasFactory;

    protected $table = "cup_rate_teams";

    protected $fillable = [
        'id', 'cup_rate_id', 'team_name', 'back', 'lay', 'status'
    ];
}
