<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\CommonTraits;
use Illuminate\Support\Facades\File;
use App\Models\Players;
use App\Models\Kundli;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlayersController extends Controller
{
    use CommonTraits;

    public function getPlayersList()
    {
        try {
            $data = Players::with('kundli')->get();

            foreach ($data as $player) {
                $kundli_data = json_decode($player->kundli, true);
                $kundli_data = json_decode($kundli_data['kundli_data'], true);
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
                $player['kundli_data'] = $houses;
            }

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

    public function getPlayer($id){
        try {
            $data = Players::find($id);
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

    public function savePlayer(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required',
                'role' => 'required',
            ]);

            $input = $request->all();
            $playerData = [
                'name' => $input['name'] ?? '',
                'date' => $input['date'] ?? '',
                'month' => $input['month'] ?? '',
                'year' => $input['year'] ?? '',
                'current_age' => $input['current_age'] ?? '',
                'birthplace' => $input['birthplace'] ?? '',
                'hours' => $input['hours'] ?? '',
                'minutes' => $input['minutes'] ?? '',
                'seconds' => $input['seconds'] ?? '',
                'latitude' => $input['latitude'] ?? '',
                'longitude' => $input['longitude'] ?? '',
                'timezone' => 5.5,
                'height' => $input['height'] ?? '',
                'role' => $input['role'] ?? '',
                'batting_style' => $input['batting_style'] ?? '',
                'bowling_style' => $input['bowling_style'] ?? '',
            ];

            if (isset($input['id'])) {
                $existingPlayer = Players::find($input['id']);
                if(isset($existingPlayer) && !empty($existingPlayer)){
                    $oldMedia = substr($existingPlayer->avatar_image, strrpos($existingPlayer->avatar_image ,"/") + 1);
                    $path = '/players/';
    
                    $oldMediaPath = public_path() . $path . $oldMedia;
    
                    if (File::exists($oldMediaPath)) {
                        unlink($oldMediaPath);
                    }
                }
            }

            $player = Players::updateOrCreate(['id' => $input['id']], $playerData);

            if ($file = $request->file('avatar_image')) {
                $path = '/players';
                $file_name = date('dmY_His') . '.' . $file->getClientOriginalExtension();
                $file->move(public_path() . $path, $file_name);
                $player->avatar_image = url($path . "/" . $file_name);
                $player->save();
            }

            if($player) {
                try {
                    $postData = array(
                        "year" => intval($input['year']),
                        "month" => intval($input['month']),
                        "date" => intval($input['date']),
                        "hours" => intval($input['hours']),
                        "minutes" => intval($input['minutes']),
                        "seconds" => intval($input['seconds']),
                        "latitude" => floatval($input['latitude']),
                        "longitude" => floatval($input['longitude']),
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

                    $kundliRecord = Kundli::where('player_id', $player->id)->first();
                    if ($kundliRecord) {
                        // Delete the existing record
                        $kundliRecord->delete();
                    } else {
                        $newKundliRecord = Kundli::create([
                            'user_id' => null,
                            'player_id' => $player->id,
                            'kundli_data' => $response
                        ]);
                    }

                } catch (\Throwable $th) {
                    Log::info($th);
                }
            }

            return response()->json([
                'data' => [],
                'success' => true,
                'msg' => 'Player Successfully Created'
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

    public function deletePlayer($id){
        try {
            $player = Players::findOrFail($id);
            $playerImg = $player->avatar_image;
            $deletSql = $player->delete();

            if ($deletSql) {
                $playerImg = substr($playerImg, strrpos($playerImg ,"/") + 1);

                $path = '/players/';

                $old_image_path = public_path() . $path . $playerImg;
                if (File::exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }

            return response()->json([
                'data' => [],
                'success' => true,
                'msg' => 'Player successfully removed'
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
