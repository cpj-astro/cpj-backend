<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\CommonTraits;
use App\Models\Matches;
use App\Models\Series;
use Illuminate\Support\Facades\DB;

class MatchController extends Controller
{
    use CommonTraits;

    public function seriesList()
    {
        try {
            $data = Series::select('series_id','series_name','series_date','total_matches','start_date','end_date','image','month_wise')
            ->where('status', 1)->orderBy('start_date', 'asc')->get();
            if(isset($data) && !empty($data) && count($data) > 0){
                return response()->json([
                    'data' => $data,
                    'success' => true,
                    'msg' => 'Data found'
                ], 200);
            }
            return response()->json([
                'data' => [],
                'success' => false,
                'msg' => 'No data found'
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            $this->captureExceptionLog($th);
            return response()->json([
                'data' => [],
                'success' => false,
                'msg' => $th->getMessage()
            ], 200);
        }
    }

    public function upcomingList()
    {
        try {
            $matchesData = Matches::select(
                // 'matches.series_id',
                'match_id',
                // 's.series_name',
                'date_wise',
                'match_date',
                'match_time',
                'matchs',
                'venue',
                'match_type',
                'min_rate',
                'max_rate',
                'fav_team',
                'team_a_id',
                'team_a',
                'team_a_short',
                'team_a_img',
                'team_b_id',
                'team_b',
                'team_b_short',
                'team_b_img',
                'match_category',
                DB::raw("STR_TO_DATE(date_wise, '%d %b %Y, %W') as formatted_date_wise"),
                DB::raw("CONCAT(STR_TO_DATE(date_wise,'%d %b %Y, %W'),' ',STR_TO_DATE(match_time, '%h:%i %p')) as formatted_date_time_wise")
            )
            // ->join('series as s', 's.series_id', '=', 'matches.series_id')
                ->where('match_category', 'upcoming')->orderBy('formatted_date_time_wise', 'asc')->get();
            if (isset($matchesData) && !empty($matchesData) && count($matchesData) > 0) {
                return response()->json([
                    'data' => $matchesData,
                    'success' => true,
                    'msg' => 'Data found'
                ], 200);
            }
            return response()->json([
                'data' => [],
                'success' => false,
                'msg' => 'No data found'
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            $this->captureExceptionLog($th);
            return response()->json([
                'data' => [],
                'success' => false,
                'msg' => $th->getMessage()
            ], 200);
        }
    }

    public function recentList()
    {
        try {
            $matchesData = Matches::select(
                // 'matches.series_id',
                'match_id',
                // 's.series_name',
                'date_wise',
                'match_date',
                'match_time',
                'matchs',
                'venue',
                'match_type',
                'min_rate',
                'max_rate',
                'fav_team',
                'team_a_id',
                'team_a',
                'team_a_short',
                'team_a_scores',
                'team_a_score',
                'team_a_over',
                'team_a_img',
                'team_a_scores_over',
                'team_b_id',
                'team_b',
                'team_b_short',
                'team_b_scores',
                'team_b_score',
                'team_b_over',
                'team_b_img',
                'team_b_scores_over',
                'result',
                'match_category',
                DB::raw("STR_TO_DATE(date_wise, '%d %b %Y, %W') as formatted_date_wise"),
                DB::raw("CONCAT(STR_TO_DATE(date_wise,'%d %b %Y, %W'),' ',STR_TO_DATE(match_time, '%h:%i %p')) as formatted_date_time_wise")
            )
            // ->join('series as s', 's.series_id', '=', 'matches.series_id')
                ->where('match_category', 'recent')->orderBy('formatted_date_time_wise', 'desc')->get();
            if (isset($matchesData) && !empty($matchesData) && count($matchesData) > 0) {
                return response()->json([
                    'data' => $matchesData,
                    'success' => true,
                    'msg' => 'Data found'
                ], 200);
            }
            return response()->json([
                'data' => [],
                'success' => false,
                'msg' => 'No data found'
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            $this->captureExceptionLog($th);
            return response()->json([
                'data' => [],
                'success' => false,
                'msg' => $th->getMessage()
            ], 200);
        }
    }
    public function liveList(){
        try {
            $matchesData = Matches::select(
                // 'matches.series_id',
                'match_id',
                // 's.series_name',
                'date_wise',
                'match_date',
                'match_time',
                'matchs',
                'venue',
                'match_type',
                'min_rate',
                'max_rate',
                'fav_team',
                's_ovr',
                's_min',
                's_max',
                'session',
                'team_a_id',
                'team_a',
                'team_a_short',
                'team_a_score',
                'team_a_over',
                'team_a_img',
                'team_b_id',
                'team_b',
                'team_b_short',
                'team_b_score',
                'team_b_over',
                'team_b_img',
                'match_category',
                DB::raw("STR_TO_DATE(date_wise, '%d %b %Y, %W') as formatted_date_wise"),
                DB::raw("CONCAT(STR_TO_DATE(date_wise,'%d %b %Y, %W'),' ',STR_TO_DATE(match_time, '%h:%i %p')) as formatted_date_time_wise")
            )
            // ->join('series as s', 's.series_id', '=', 'matches.series_id')
                ->where('match_category', 'live')->orderBy('formatted_date_time_wise', 'asc')->get();
            if (isset($matchesData) && !empty($matchesData) && count($matchesData) > 0) {
                return response()->json([
                    'data' => $matchesData,
                    'success' => true,
                    'msg' => 'Data found'
                ], 200);
            }
            return response()->json([
                'data' => [],
                'success' => false,
                'msg' => 'No data found'
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            $this->captureExceptionLog($th);
            return response()->json([
                'data' => [],
                'success' => false,
                'msg' => $th->getMessage()
            ], 200);
        }
    }

    public function matchInfo(Request $request)
    {
        try {
            $match_id = $request->input('match_id');
            if (isset($match_id) && !empty($match_id)) {
                $matchesData = Matches::select(
                    'matches.series_id',
                    's.series_name',
                    'match_id',
                    'date_wise',
                    'match_date',
                    'match_time',
                    'matchs',
                    'venue',
                    'match_type',
                    'min_rate',
                    'max_rate',
                    'fav_team',
                    'team_a_id',
                    'team_a',
                    'team_a_short',
                    'team_a_img',
                    'team_a_score',
					'team_a_scores',
					'team_a_over',
                    'team_b_id',
                    'team_b',
                    'team_b_short',
                    'team_b_img',
                    'team_b_score',
					'team_b_scores',
					'team_b_over',
                    'matches.toss',
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
                    'match_category',
                    'fancy_info',
                    'pitch_report',
                    'weather',
                    'session',
                    'result',
                    DB::raw("STR_TO_DATE(date_wise, '%d %b %Y, %W') as formatted_date_wise")
                )
                ->leftJoin('series as s', 's.series_id', '=', 'matches.series_id')
                ->where('match_id', $match_id)->where('matches.status', 1)->first();
                if (isset($matchesData) && !empty($matchesData)) {
                    $matchesData->bolwer = $matchesData->bowler;
                    return response()->json([
                        'data' => $matchesData,
                        'success' => true,
                        'msg' => 'Data found'
                    ], 200);
                }
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'msg' => 'No data found'
                ], 200);
            } else {
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'msg' => 'No match id'
                ], 200);
            }
        } catch (\Throwable $th) {
            $this->captureExceptionLog($th);
            return response()->json([
                'data' => [],
                'success' => false,
                'msg' => $th->getMessage()
            ], 200);
        }
    }
}
