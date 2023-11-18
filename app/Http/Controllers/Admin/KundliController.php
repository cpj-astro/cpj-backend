<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\CommonTraits;
use Illuminate\Support\Facades\File;
use App\Models\Players;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KundliController extends Controller
{
    use CommonTraits;

    public function saveKundli(Request $request)
    {
        try {
            $input = $request->all();

            // Prepare the data array
            $postData = array(
                "year" => intval($input['year']),
                "month" => intval($input['month']),
                "date" => intval($input['date']),
                "hours" => intval($input['hours']),
                "minutes" => intval($input['minutes']),
                "seconds" => intval($input['seconds']),
                "latitude" => floatval($input['latitude']),
                "longitude" => floatval($input['longitude']),
                "timezone" => floatval($input['timezone']),
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
            
            return response()->json([
                'data' => $response,
                'success' => true,
                'msg' => 'Kundli Successfully Created'
            ], 201); // 201 Created status code
        } catch (\Throwable $th) {
            $this->captureExceptionLog($th);
            return response()->json([
                'data' => [],
                'success' => false,
                'msg' => 'An error occurred'
            ], 500); // 500 Internal Server Error status code
        }
    }

}
