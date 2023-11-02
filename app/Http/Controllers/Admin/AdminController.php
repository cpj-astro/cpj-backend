<?php

namespace App\Http\Controllers\Admin;

use App\Models\CupRate;
use App\Models\CupRateTeams;
use App\Models\Matches;
use App\Models\Series;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Traits\CommonTraits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    use CommonTraits;
    public function addMatch(Request $request)
    {
        try {
            $input = $request->all();
            if (isset($input['match_id']) && !empty($input['match_id'])) {
                $matchId = $input['match_id'];
                $matchData = [
                    'team_a' => $input["team_a"],
                    'team_b' => $input["team_b"],
                    'team_a_short' => $input["team_a_short"],
                    'team_b_short' => $input["team_b_short"],
                    'team_a_id' => $input["team_a_id"],
                    'team_b_id' => $input["team_b_id"],
                    'match_date' => $input["date"],
                    'match_time' => $input["time"],
                    'datewise' => $input["day"],
                    'venue' => $input["ground_name"],
                    'team_a_img' => $input["team_a_img"],
                    'team_b_img' => $input["team_b_img"],
                    'toss' => $input["toss"],
                    'umpire' => $input["umpire"],
                    'third_umpire' => $input["third_umpire"],
                    'referee' => $input["referee"],
                    'matchs' => $input["matchs"],
                    'series_id' => $input["series_name"],
                    'match_id' => $input["match_id"],
                    'match_category' => $input["match_category"],
                ];
                $n = Matches::create($matchData);
                if($n) {
                    return response()->json([
                        'data' => [],
                        'success' => true,
                        'msg' => 'Data inserted successfully'
                    ], 200);
                } else {
                    return response()->json([
                        'data' => [],
                        'success' => false,
                        'msg' => 'Cannot insert data'
                    ], 200);
                }
            } else {
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'msg' => 'No match_id available'
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
    public function updateMatch(Request $request)
    {
        try {
            $input = $request->all();
            if (isset($input['match_id']) && !empty($input['match_id'])) {
                $matchId = $input['match_id'];
                unset($input['match_id']);
                if(isset($input['match_category']) && !empty($input['match_category']) && $input['match_category'] == 'live'){
                    $input['source'] = 'admin';
                }
                $n = Matches::where('match_id', $matchId)->update($input);
                $data = Matches::select('*', 'yet_to_bat as yet_to_bet')->where('match_id', $matchId)->first();
                if ($n) {
                    return response()->json([
                        'data' => $data,
                        'success' => true,
                        'msg' => 'Data updated successfully'
                    ], 200);
                } else {
                    return response()->json([
                        'data' => [],
                        'success' => false,
                        'msg' => 'Cannot update data'
                    ], 200);
                }
            } else {
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'msg' => 'No match_id available'
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
    public function updateSeries(Request $request)
    {
        try {
            $input = $request->all();
            if (isset($input['series_id']) && !empty($input['series_id'])) {
                $seriesId = $input['series_id'];
                unset($input['series_id']);
                $n = Series::where('series_id', $seriesId)->update($input);
                if ($n) {
                    return response()->json([
                        'data' => [],
                        'success' => true,
                        'msg' => 'Data updated successfully'
                    ], 200);
                } else {
                    return response()->json([
                        'data' => [],
                        'success' => false,
                        'msg' => 'Cannot update data'
                    ], 200);
                }
            } else {
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'msg' => 'No series_id available'
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

    public function updateCupRates(Request $request)
    {
        try {
            $input = $request->all();
            if (isset($input['data']) && !empty($input['data'])) {
                foreach ($input['data'] as $key => $value) {
                    $params = [
                        'title' => $value['title'],
                        'sub_title' => $value['sub_title'],
                        'sequence_number' => $value['sequence_number'],
                        'status' => $value['status']
                    ];

                    $res = CupRate::updateOrCreate(['id' => $value['id']], $params);
                    if ($res && isset($res->id) && !empty($res->id)) {
                        foreach ($value['get_all_cup_rates_teams'] as $keyI => $valueI) {
                            $paramsI = [
                                'cup_rate_id' => $res->id,
                                'team_name' => $valueI['team_name'],
                                'back' => $valueI['back'],
                                'lay' => $valueI['lay'],
                                'status' => $valueI['status']
                            ];
                            $resCRT = CupRateTeams::updateOrCreate(['id' => $valueI['id']], $paramsI);
                        }
                    }
                    // $this->captureLog($res);
                }
            }
            if (isset($input['delete_id']) && !empty($input['delete_id'])) {
                CupRateTeams::whereIn('cup_rate_id', $input['delete_id'])->delete();
                CupRate::whereIn('id', $input['delete_id'])->delete();
            }
            if (isset($input['delete_team_id']) && !empty($input['delete_team_id'])) {
                CupRateTeams::whereIn('id', $input['delete_team_id'])->delete();
            }
            return response()->json([
                'data' => [],
                'success' => true,
                'msg' => 'Data inserted/updated/deleted successfully'
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

    public function getAllCupRates()
    {
        try {
            $data = CupRate::with('getAllCupRatesTeams')->get();
            if ($data) {
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

    public function getAllSettings()
    {
        try {
            $data = Setting::get()->toArray();
            if ($data) {
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
            $this->captureExceptionLog($th);
            return response()->json([
                'data' => [],
                'success' => false,
                'msg' => $th->getMessage()
            ], 200);
        }
    }

    public function updateSettings(Request $request)
    {
        try {
            $input = $request->all();
            if (isset($input['current_app_version']) && !empty($input['current_app_version'])) {
                Setting::where('setting_key', 'current_app_version')->update(['setting_value' => $input['current_app_version']]);
            }
            if (isset($input['show_cup_rate_button']) && !empty($input['show_cup_rate_button'])) {
                Setting::where('setting_key', 'show_cup_rate_button')->update(['setting_value' => $input['show_cup_rate_button']]);
            }
            if (isset($input['tv_setting']) && !empty($input['tv_setting'])) {
                Setting::where('setting_key', 'tv_setting')->update(['setting_value' => $input['tv_setting']]);
            }
            return response()->json([
                'data' => [],
                'success' => true,
                'msg' => 'Data updated successfully'
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

    public function sendNotification(Request $request)
    {
        try {
            $input = $request->all();
            $rules = [
                'topic' => 'required|in:mango777allusers,allusers',
                'body' => 'required',
                'title' => 'required',
            ];
            $messages = [
                'topic.required' => 'Topic is mandatory',
                'body.required' => 'Body is mandatory',
                'title.required' => 'Title is mandatory',
            ];

            $validator = Validator::make($input, $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'msg' => $validator
                ], 200);
            }
            $params = [
                'to' => '/topics/'.trim($input['topic']),
                'notification' => [
                    "body" => $input['body'],
                    "OrganizationId" => "2",
                    "content_available" => true,
                    "priority" => "high",
                    // "subtitle"=> "Elementary School",
                    "title" => $input['title']
                ],
                "data" => [
                    "priority" => "high",
                    // "sound" => "app_sound.wav",
                    "content_available" => true,
                    // "bodyText" => "New Announcement assigned",
                    // "organization" => "Elementary school"
                ]

            ];
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($params),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . config('services.fcm_notification_token'),
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            return response()->json([
                'data' => $response,
                'success' => true,
                'msg' => 'Notification send successfully'
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
}
