<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teams extends Model
{
    use HasFactory;

    protected $table = "teams";

    protected $fillable = [
        'id',
        'match_id',
        'team_name',
        'p1',
        'p2',
        'p3',
        'p4',
        'p5',
        'p6',
        'p7',
        'p8',
        'p9',
        'p10',
        'p11',
        'captain',
        'vice_captain',
        'bowler',
        'batsman',
        'wicket_keeper',
        'all_rounder',
        'status',
        'created_at',
        'updated_at',
    ];
}
