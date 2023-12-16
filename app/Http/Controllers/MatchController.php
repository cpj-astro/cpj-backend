<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\CommonTraits;
use App\Models\Matches;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MatchController extends Controller
{
    use CommonTraits;
    public function __construct()
    {
    }
    public function getList(Request $request)
    {
        try {
            $series_id = $request->input('series_id');
            if (isset($series_id) && !empty($series_id)) {
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
                    'session',
                    'result',
                    DB::raw("STR_TO_DATE(date_wise, '%d %b %Y, %W') as formatted_date_wise"),
                    DB::raw("CONCAT(STR_TO_DATE(date_wise,'%d %b %Y, %W'),' ',STR_TO_DATE(match_time, '%h:%i %p')) as formatted_date_time_wise")
                )
                ->leftJoin('series as s', 's.series_id', '=', 'matches.series_id')
                ->where('matches.series_id', $series_id)->orderBy('formatted_date_time_wise', 'asc')->get();
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
            } else {
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'msg' => 'No series id'
                ], 200);
            }
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

    public function dashboardList(){
        try {
            $sqlCols = ['matches.series_id',
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
            'team_b_id',
            'team_b',
            'team_b_short',
            'team_b_img',
            'team_b_score',
            'matches.toss',
            'back1',
            'back2',
            'back3',
            'lay1',
            'lay2',
            'lay3',
            'match_over',
            'team_a_scores_over',
            'team_b_scores_over',
            'match_category',
            'result',
            DB::raw("STR_TO_DATE(date_wise, '%d %b %Y, %W') as formatted_date_wise"),
            DB::raw("CONCAT(STR_TO_DATE(date_wise,'%d %b %Y, %W'),' ',STR_TO_DATE(match_time, '%h:%i %p')) as formatted_date_time_wise")
        ];

            $liveMatchesData = Matches::select($sqlCols)->where('match_category', 'live')
            ->join('series as s', 's.series_id', '=', 'matches.series_id')
            ->limit(3)->get()->toArray();

            $upcomingMatchesData = Matches::select($sqlCols)->where('match_category', 'upcoming')
            ->join('series as s', 's.series_id', '=', 'matches.series_id')->orderBy('formatted_date_time_wise', 'asc')
            ->limit(3)->get()->toArray();

            $recentMatchesData = Matches::select($sqlCols)->where('match_category', 'recent')
            ->join('series as s', 's.series_id', '=', 'matches.series_id')->orderBy('formatted_date_time_wise', 'desc')
            ->limit(3)->get()->toArray();

            $matchesData = [];
            if(!empty($liveMatchesData) && count($liveMatchesData) > 0){
                $matchesData = array_merge($matchesData, $liveMatchesData);
            }
            if(!empty($upcomingMatchesData) && count($upcomingMatchesData) > 0){
                $matchesData = array_merge($matchesData, $upcomingMatchesData);
            }
            if(!empty($recentMatchesData) && count($recentMatchesData) > 0){
                $matchesData = array_merge($matchesData, $recentMatchesData);
            }

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

    public function getUpcomingList()
    {
        try {
            $matchesData = Matches::select(
                'matches.series_id',
                'match_id',
                's.series_name',
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
            ->join('series as s', 's.series_id', '=', 'matches.series_id')
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

    public function getRecentList()
    {
        try {
            $matchesData = Matches::select(
                'matches.series_id',
                'match_id',
                's.series_name',
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
            ->join('series as s', 's.series_id', '=', 'matches.series_id')
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

    public function scorecard(Request $request)
    {
        try {
            $apiUrl = config('services.cricket-champion.endpoint') . 'scorecardByMatchId/' . config('services.cricket-champion.token');
            $match_id = $request->input('match_id');
            if (isset($match_id) && !empty($match_id)) {
                $postData = [
                    'match_id' => $match_id
                ];
                $res = $this->pullData($apiUrl, 'POST', $postData);

                // comment the below before live
                // $path = public_path() . "/scorecardByMatchId.json";
                // $res = File::get($path);

                $res = json_decode($res, true);

                if ($res['status']) {
                    return response()->json([
                        'data' => $res['data'],
                        'success' => true,
                        'msg' => 'Data found'
                    ], 200);
                } else {
                    return response()->json([
                        'data' => [],
                        'success' => false,
                        'msg' => (isset($res['msg']) ? $res['msg'] : 'No match id')
                    ], 200);
                }
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

    public function matchInfo(Request $request)
    {
        try {
            $userId = auth()->id(); 
            $match_id = $request->input('match_id');
            if (isset($match_id) && !empty($match_id)) {
                $matchesData = Matches::select(
                    'matches.series_id',
                    's.series_name',
                    'matches.match_id',
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
                    DB::raw("STR_TO_DATE(date_wise, '%d %b %Y, %W') as formatted_date_wise"),
                    DB::raw("CONCAT(STR_TO_DATE(date_wise,'%d %b %Y, %W'),' ',STR_TO_DATE(match_time, '%h:%i %p')) as formatted_date_time_wise"),
                    'payments.id as payment_id',
                    'payments.razorpay_payment_id',
                    'payments.razorpay_order_id',
                    'payments.razorpay_signature',
                    'payments.amount as payment_amount',
                    'payments.status as payment_status',
                    'payments.created_at as payment_created',
                    'payments.updated_at as payment_updated',
                    'astrology_data'
                )
                ->leftJoin('payments', function($join) use ($userId) {
                    $join->on('matches.match_id', '=', 'payments.match_id')
                        ->where('payments.user_id', '=', $userId);
                })
                ->leftJoin('match_astrology', function($join) use ($userId) {
                    $join->on('matches.match_id', '=', 'match_astrology.match_id')
                        ->where('match_astrology.user_id', '=', $userId);
                })
                ->leftJoin('series as s', 's.series_id', '=', 'matches.series_id')
                ->where('matches.match_id', $match_id)->where('matches.status', 1)->first();
                
                return response()->json([
                    'data' => $matchesData,
                    'success' => true,
                    'msg' => 'Data found'
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
    
    public function allMatches(Request $request){
        try {
            $userId = $request->get('user_id');
            $userExisted = User::find($userId);
            $userStatus = 'failed';
            if($userExisted) {
                $userStatus = 'success';
            }
            $matchesData = Matches::select(
                'matches.series_id',
                's.series_name',
                'matches.match_id',
                'astrology_status',
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
                DB::raw("STR_TO_DATE(date_wise, '%d %b %Y, %W') as formatted_date_wise"),
                DB::raw("CONCAT(STR_TO_DATE(date_wise,'%d %b %Y, %W'),' ',STR_TO_DATE(match_time, '%h:%i %p')) as formatted_date_time_wise"),
                'payments.id as payment_id',
                'payments.razorpay_payment_id',
                'payments.razorpay_order_id',
                'payments.razorpay_signature',
                'payments.amount as payment_amount',
                'payments.status as payment_status',
                'payments.created_at as payment_created',
                'payments.updated_at as payment_updated',
                'astrology_data'
            )
            ->leftJoin('payments', function($join) use ($userId) {
                $join->on('matches.match_id', '=', 'payments.match_id')
                    ->where('payments.user_id', '=', $userId);
            })
            ->leftJoin('match_astrology', function($join) use ($userId) {
                $join->on('matches.match_id', '=', 'match_astrology.match_id')
                    ->where('match_astrology.user_id', '=', $userId);
            })
            ->leftJoin('series as s', 's.series_id', '=', 'matches.series_id')
            ->whereIn('match_category',  ['live', 'upcoming'])
            ->orderBy('formatted_date_time_wise', 'asc')
            ->get();
            
            if (isset($matchesData) && !empty($matchesData) && count($matchesData) > 0) {
                return response()->json([
                    'data' => $matchesData,
                    'success' => true,
                    'user_status' => $userStatus,
                    'msg' => 'Data found'
                ], 200);
            }
    
            return response()->json([
                'data' => [],
                'success' => false,
                'user_status' => $userStatus,
                'msg' => 'No data found'
            ], 200);
        } catch (\Throwable $th) {
            $this->captureExceptionLog($th);
            return response()->json([
                'data' => [],
                'success' => false,
                'user_status' => $userStatus,
                'msg' => $th->getMessage()
            ], 200);
        }
    }
    
    public function getLiveList(){
        try {
            $userId = auth()->id(); // Assuming you are using Laravel's built-in authentication
    
            $matchesData = Matches::select(
                'matches.series_id',
                's.series_name',
                'matches.match_id',
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
                DB::raw("CONCAT(STR_TO_DATE(date_wise,'%d %b %Y, %W'),' ',STR_TO_DATE(match_time, '%h:%i %p')) as formatted_date_time_wise"),
                'payments.id as payment_id',
                'payments.razorpay_payment_id',
                'payments.razorpay_order_id',
                'payments.razorpay_signature',
                'payments.amount as payment_amount',
                'payments.status as payment_status',
                'payments.created_at as payment_created',
                'payments.updated_at as payment_updated'
            )
                ->leftJoin('payments', function($join) use ($userId) {
                    $join->on('matches.match_id', '=', 'payments.match_id')
                        ->where('payments.user_id', '=', $userId);
                })
                ->join('series as s', 's.series_id', '=', 'matches.series_id')
                ->where('match_category', 'live')
                ->orderBy('formatted_date_time_wise', 'asc')
                ->get();
    
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
            $this->captureExceptionLog($th);
            return response()->json([
                'data' => [],
                'success' => false,
                'msg' => $th->getMessage()
            ], 200);
        }
    }
    
    public function getOddHistory(Request $request)
    {
        try {
            $apiUrl = config('services.cricket-champion.endpoint') . 'matchOddHistory/' . config('services.cricket-champion.token');
            $match_id = $request->input('match_id');
            if (isset($match_id) && !empty($match_id)) {
                $postData = [
                    'match_id' => $match_id
                ];
                $res = $this->pullData($apiUrl, 'POST', $postData);

                // comment the below before live
                // $path = public_path() . "/matchOddHistory.json";
                // $res = File::get($path);

                $res = json_decode($res, true);

                if ($res['status']) {
                    return response()->json([
                        'data' => $res['data'],
                        'success' => true,
                        'msg' => 'Data found'
                    ], 200);
                } else {
                    return response()->json([
                        'data' => [],
                        'success' => false,
                        'msg' => (isset($res['msg']) ? $res['msg'] : 'No match id')
                    ], 200);
                }
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

    public function squadByMatchId(Request $request){
        try {
            $apiUrl = config('services.cricket-champion.endpoint') . 'squadByMatchId/' . config('services.cricket-champion.token');
            $match_id = $request->input('match_id');
            if (isset($match_id) && !empty($match_id)) {
                $postData = [
                    'match_id' => $match_id
                ];
                $res = $this->pullData($apiUrl, 'POST', $postData);

                // comment the below before live
                // $path = public_path() . "/matchOddHistory.json";
                // $res = File::get($path);

                $res = json_decode($res, true);

                if ($res['status']) {
                    return response()->json([
                        'data' => $res['data'],
                        'success' => true,
                        'msg' => 'Data found'
                    ], 200);
                } else {
                    return response()->json([
                        'data' => [],
                        'success' => false,
                        'msg' => (isset($res['msg']) ? $res['msg'] : 'No match id')
                    ], 200);
                }
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

    public function commentary(Request $request){
        try {
            $apiUrl = config('services.cricket-champion.endpoint') . 'commentary/' . config('services.cricket-champion.token');
            $match_id = $request->input('match_id');
            if (isset($match_id) && !empty($match_id)) {
                $postData = [
                    'match_id' => $match_id
                ];
                $res = $this->pullData($apiUrl, 'POST', $postData);

                // comment the below before live
                // $path = public_path() . "/matchOddHistory.json";
                // $res = File::get($path);

                $res = json_decode($res, true);

                if ($res['status']) {
                    return response()->json([
                        'data' => $res['data'],
                        'success' => true,
                        'msg' => 'Data found'
                    ], 200);
                } else {
                    return response()->json([
                        'data' => [],
                        'success' => false,
                        'msg' => (isset($res['msg']) ? $res['msg'] : 'No match id')
                    ], 200);
                }
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
