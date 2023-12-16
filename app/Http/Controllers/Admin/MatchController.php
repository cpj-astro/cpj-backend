<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\CommonTraits;
use App\Models\Matches;
use App\Models\Series;
use App\Models\Kundli;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
                'matches.series_id',
                'match_id',
                's.series_name',
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

    public function recentList()
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
    public function liveList(){
        try {
            $matchesData = Matches::select(
                'matches.series_id',
                'matches.match_id',
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
                'p.id as payment_id',
                'p.razorpay_payment_id',
                'p.razorpay_order_id',
                'p.razorpay_signature',
                'p.amount as payment_amount',
                'p.status as payment_status',
                'p.created_at as payment_created',
                'p.updated_at as payment_updated',
                'ma.astrology_data',
                'k.kundli_data'
            )
            ->leftJoin('payments as p', 'p.match_id', '=', 'matches.match_id')
            ->leftJoin('kundli as k', 'k.match_id', '=', 'matches.match_id')
            ->leftJoin('match_astrology as ma', 'ma.match_id', '=', 'matches.match_id')
            ->join('series as s', 's.series_id', '=', 'matches.series_id')
            ->where('match_category', 'live')->orderBy('formatted_date_time_wise', 'asc')->get();

            if (isset($matchesData) && !empty($matchesData) && count($matchesData) > 0) {
                foreach ($matchesData as $match) {
                    if(isset($match->kundli_data)){
                        $kundli_data = json_decode($match->kundli_data, true);
                        $planetaryData = $kundli_data['output'][1];
            
                        // Create an array to represent the 12 houses, initially filled with null values
                        $houses = array_fill(0, 12, null);
            
                        // Iterate through planetary data and map planets to houses
                        foreach ($planetaryData as $planetName => $planetInfo) {
                            $currentSign = $planetInfo['current_sign'];
            
                            // Map the current_sign to the corresponding house number (subtract 1 since arrays are zero-based)
                            $houseNumber = $currentSign - 1;
            
                            // Initialize the house array if it doesn't exist
                            if (!isset($houses[$houseNumber])) {
                                $houses[$houseNumber] = [];
                            }
            
                            
                            // Add the first 3 letters of the planet's name to the house's array
                            $houses[$houseNumber][] = substr($planetName, 0, 2);
                        }
                        $match['kundli_data'] = $houses;
                    }
                }
    
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

    public function saveMatchKundli(Request $request) {
        try {
            $input = $request->all();
            
            // Extract year, month, and date from match_date
            $matchDate = Carbon::parse($input['match_date']);
            $year = intval($matchDate->format('Y'));
            $month = intval($matchDate->format('m'));
            $date = intval($matchDate->format('d'));

            // Extract hours and minutes from match_time
            $matchTime = Carbon::parse($input['match_time']);
            $hours = intval($matchTime->format('H'));
            $minutes = intval($matchTime->format('i'));

            $postData = array(
                "year" => $year,
                "month" => $month,
                "date" => $date,
                "hours" => $hours,
                "minutes" => $minutes,
                "seconds" => 0,
                "latitude" => 28.7041,
                "longitude" => 77.1025,
                "timezone" => 5.5,
                "settings" => array(
                    "observation_point" => "topocentric",
                    "ayanamsha" => "lahiri"
                )
            );

            $postDataJSON = json_encode($postData);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://json.freeastrologyapi.com/planets',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postDataJSON, // Send the JSON data
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'x-api-key: fPfIcf5yoT2ObLpUGLwt85bZs1XtTFGK573TVK8A'
                ),
            ));

            $response = curl_exec($curl);

            if (curl_errno($curl)) {     
               $error_msg = curl_error($curl); 
               Log::info($error_msg);
            } 

            curl_close($curl);

            $kundliRecord = Kundli::where('match_id', $input['match_id'])->first();

            if ($kundliRecord) {
                // Delete the existing record
                $kundliRecord->delete();
            } else {
                $newKundliRecord = Kundli::create([
                    'match_id' => $input['match_id'],
                    'kundli_data' => $response
                ]);
            }

            return response()->json([
                'data' => $response,
                'success' => true,
                'msg' => 'Match Kundli Successfully Created'
            ], 201); // 201 Created status code
        } catch (\Throwable $th) {
            $this->captureExceptionLog($th);
            return response()->json([
                'data' => [],
                'success' => false,
                'msg' => 'An error occurred'
            ], 200); // 500 Internal Server Error status code
        }
    }
}
