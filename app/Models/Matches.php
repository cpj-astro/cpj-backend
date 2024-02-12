<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Series;
use App\Models\MatchAstrology;

class Matches extends Model
{
    use HasFactory;

    protected $table = "matches";

    protected $fillable = [
        'series_id',
        'match_id',
        'date_wise',
        'match_date',
        'match_time',
        'matchs',
        'venue',
        'match_type',
        'astrology_status',
        'min_rate',
        'max_rate',
        'fav_team',
        'result',
        'team_a_id',
        'team_a',
        'team_a_short',
        'team_a_img',
        'team_a_scores',
        'team_a_score',
        'team_a_over',
        'team_b_id',
        'team_b',
        'team_b_short',
        'team_b_img',
        'team_b_score',
        'team_b_scores',
        'team_b_over',
        's_ovr', 's_min', 's_max', 'session', 'toss', 'umpire', 'third_umpire', 'referee', 'man_of_match',
        'match_category',
        'source',
        'status',
        'back1',
        'back2',
        'back3',
        'lay1',
        'lay2',
        'lay3',
        'batsman',
        'bowler',
        'curr_rate',
        'first_circle',
        'fancy',
        'last4overs',
        'lastwicket',
        'match_over',
        'partnership',
        'rr_rate',
        'second_circle',
        'target',
        'team_a_scores_over',
        'team_b_scores_over',
        'yet_to_bat',
        'fancy_info',
        'pitch_report',
        'weather',
        'updated_at',
        'created_at'
    ];

    protected $casts = [
        'batsman' => 'array',
        'last4overs' => 'array',
        'last36ball' => 'array',
        'fancy_info' => 'array',
        'bowler' => 'object',
        'lastwicket' => 'object',
        'match_completed' => 'object',
        'match_tied' => 'object',
        'yet_to_bat' => 'array',
        'partnership' => 'object',
        'team_a_score' => 'object',
        'team_b_score' => 'object',
        'match_id' => 'integer'
    ];

    public function series()
    {
        return $this->hasOne(Series::class, 'series_id', 'series_id');
        // return $this->hasOne(Series::class, 'series_id', 'id')->where('user_id', auth()->user()->id);
    }

    public function astrology() {
        return $this->hasOne(MatchAstrology::class, 'match_id', 'match_id')->where('user_id', auth()->user()->id);
    }

    public function successPayment() {
        return $this->hasOne(Payment::class, 'match_id', 'match_id')->where('user_id', auth()->user()->id)->where('status', 'success');
    }

    // public function getTeamAOverAttribute($value)
    // {
    //     return (isset($value) && !empty($value)) ? $value + 0 : $value;
    // }
    // public function getTeamBOverAttribute($value)
    // {
    //     return (isset($value) && !empty($value)) ? $value + 0 : $value;
    // }
}
