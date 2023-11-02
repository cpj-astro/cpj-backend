<?php
namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait CommonTraits {

    public function captureExceptionLog($ex, $title=''){
        Log::error("Error occured at ".$title);
        Log::error("Exception :" . $ex->getMessage() . ' : ' . $ex->getLine());
        Log::error("Traces : " . $ex->getTraceAsString());
    }
    public function captureLog($message, $type='info'){
        if($type=='info'){
            Log::info($message);
        }
        else if($type == 'error'){
            Log::error($message);
        }
        else if($type == 'debug'){
            Log::debug($message);
        }
    }

    public function pullData($url, $method, $postData = []){
        try {
            $ch = curl_init();
            // curl_setopt($ch, CURLOPT_PROXY, env('PROXY_URL'));
            // curl_setopt($ch, CURLOPT_PROXYUSERPWD, env('PROXYUSERPWD'));
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 0);
            if ($method == 'POST' || $method == 'PATCH') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                // curl_setopt($ch, CURLOPT_POSTFIELDS, "match_id=2449");
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type:application/x-www-form-urlencoded',
                'Accept:application/json'
            ]);
            $response = curl_exec($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $http_error = curl_error($ch);
            curl_close($ch);
            if(isset($response) && !empty($response)){
                return $response;
            }
            return [
                'status' => false,
                'msg' => 'Not response from API',
            ];
        } catch (\Throwable $th) {
            //throw $th;
            $this->captureExceptionLog($th, 'PullData trait');
            return [
                'status' => false,
                'data' => [],
                'msg' => $th->getMessage()
            ];
        }
    }

    public function prepareMatchData($data)
    {
        $newData = [];
        if ($data['match_type'] == "Test") {
            // This is for TEST
            $newData['back1'] = $data['min_rate'];
            $newData['lay1'] = $data['max_rate'];

            if(isset($data['min_rate_1'])){
                $newData['back2'] = $data['min_rate_1'];
            }
            if(isset($data['max_rate_1'])){
                $newData['lay2'] = $data['max_rate_1'];
            }

            if(isset($data['min_rate_2'])){
                $newData['back3'] = $data['min_rate_2'];
            }
            if(isset($data['max_rate_2'])){
                $newData['lay3'] = $data['max_rate_2'];
            }

        } else {
            // This is for T10, T20 and ODI
            if ($data['team_a_short'] == $data['fav_team']) {
                $newData['back1'] = $data['min_rate'];
                $newData['lay1'] = $data['max_rate'];

                if ($newData['back1'] != "" && !empty($newData['back1'])) {
                    $newData['back2'] = (1 / ($data['max_rate'] - 1)) + 1;
                    $newData['back2'] = number_format($newData['back2'], 2);

                    $newData['lay2'] = (1 / ($data['min_rate'] - 1)) + 1;
                    $newData['lay2'] = number_format($newData['lay2'], 2);
                } else {
                    $newData['back1'] = 0;
                    $newData['back2'] = 1000;
                    $newData['lay2'] = 0;
                }
            } else if ($data['team_b_short'] == $data['fav_team']) {
                $newData['back2'] = $data['min_rate'];
                $newData['lay2'] = $data['max_rate'];

                if ($newData['back2'] != "" && !empty($newData['back2'])) {
                    $newData['back1'] = (1 / ($data['max_rate'] - 1)) + 1;
                    $newData['back1'] = number_format($newData['back1'], 2);

                    $newData['lay1'] = (1 / ($data['min_rate'] - 1)) + 1;
                    $newData['lay1'] = number_format($newData['lay1'], 2);
                } else {
                    $newData['back2'] = 0;
                    $newData['back1'] = 1000;
                    $newData['lay1'] = 0;
                }
            } else {
                $newData['back1'] = 1.90;
                $newData['back2'] = 1.90;
                $newData['lay1'] = 2.00;
                $newData['lay2'] = 2.00;
            }
        }

        return $newData;
    }
}