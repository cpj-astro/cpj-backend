<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Models\Payment;
use App\Models\GameJob;
use App\Models\OnlineVisitors;
use App\Models\Reviews;
use App\Models\MatchAstrology;
use App\Models\UserApiRequest;
use Illuminate\Http\Request;
use App\Traits\CommonTraits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use CommonTraits;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = User::with('kundli')->where('user_type', 1)->get();

            foreach ($data as $user) {
                $kundli_data = json_decode($user->kundli, true);

                if (isset($kundli_data['kundli_data'])) {
                    $kundli_data = json_decode($kundli_data['kundli_data'], true);

                    if (isset($kundli_data['output'][1])) {
                        $planetaryData = $kundli_data['output'][1];

                        // Create an array to represent the 12 houses, initially filled with null values
                        $houses = array_fill(0, 12, null);

                        // Iterate through planetary data and map planets to houses
                        foreach ($planetaryData as $planetName => $planetInfo) {
                            // Check if the required keys exist before accessing them
                            if (isset($planetInfo['current_sign'])) {
                                $currentSign = $planetInfo['current_sign'];

                                // Map the current_sign to the corresponding house number (subtract 1 since arrays are zero-based)
                                $houseNumber = $currentSign - 1;

                                // Initialize the house array if it doesn't exist
                                if (!isset($houses[$houseNumber])) {
                                    $houses[$houseNumber] = [];
                                }
                                
                                // Add the first 3 letters of the planet's name to the house's array
                                $houses[$houseNumber][] = substr($planetName, 0, 2);
                                if(substr($planetName, 0, 2) == 'Mo') {
                                    $user['moon_sign'] = $currentSign;
                                }
                            }
                        }

                        $user['kundli_data'] = $houses;
                    } else {
                        $user['kundli_data'] = [];
                    }
                } else {
                    $user['kundli_data'] = [];
                }
            }

            if (!empty($data)) {
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

    
    public function getUserDetails($id) {
        // Check if the user is logged in
        if ($id) {
            $user = User::find($id);
            $userId = $user->id;
            $userPaymentDetails = Payment::with('user', 'match', 'pandit')->where('user_id', $userId)->get();
            
            foreach ($userPaymentDetails as $payment) {
                $matchId = $payment->match_id;
    
                // Assuming there is a MatchAstrology model for the match_astrology table
                $matchAstrologyDetails = MatchAstrology::where('match_id', $matchId)->where('user_id', $userId)->first();
    
                // Append match details to the payment record
                $payment->match_astrology_details = $matchAstrologyDetails;
            }
            $planetaryData = [];
            if($user->kundli) {
                $kundli_data = json_decode($user->kundli, true);
                $kundli_data = json_decode($kundli_data['kundli_data'], true);
                $planetaryData = $kundli_data['output'][1];
            }
    
            // Create an array to represent the 12 houses, initially filled with null values
            $houses = array_fill(0, 12, null);
            $housesDesc = array_fill(0, 12, null);
            
            $houseSigns = [
                'Aries', 'Taurus', 'Gemini', 'Cancer', 'Leo', 'Virgo', 'Libra', 'Scorpio', 'Sagittarius', 'Capricorn', 'Aquarius', 'Pisces'
            ];

            // Iterate through planetary data and map planets to houses
            foreach ($planetaryData as $planetName => $planetInfo) {
                $currentSign = $planetInfo['current_sign'];
    
                // Map the current_sign to the corresponding house number (subtract 1 since arrays are zero-based)
                $houseNumber = $currentSign - 1;
    
                // Initialize the house array if it doesn't exist
                if (!isset($houses[$houseNumber])) {
                    $houses[$houseNumber] = [];
                }

                if (!isset($housesDesc[$houseNumber])) {
                    $housesDesc[$houseNumber] = [];
                }

                $housesDesc[$houseNumber][] = $planetName;
                // Add the first 3 letters of the planet's name to the house's array
                $houses[$houseNumber][] = substr($planetName, 0, 2);
                if(substr($planetName, 0, 2) == 'Mo') {
                    $user['moon_sign'] = $currentSign;
                    $user['sign_name'] = $houseSigns[$currentSign - 1];
                }
            }
    
            // Convert each house's array into a comma-separated string
            foreach ($houses as $houseNumber => $houseArray) {
                if (is_array($houseArray)) {
                    $houses[$houseNumber] = implode(', ', $houseArray);
                }
            }
    
            // Iterate through houses and create descriptions
            $kundli_description = [];

            foreach ($housesDesc as $houseNumber => $planetInfo) {
                $houseNumber++; // Increment by 1 to match house numbering
            
                if ($planetInfo === null) {
                    $kundli_description[] = "In {$houseNumber} house there are no planets";
                } else {
                    if (is_array($planetInfo)) {
                        $planets = implode(' and ', array_filter($planetInfo, 'is_string'));
                        $planetCount = count(array_filter($planetInfo, 'is_string'));
                        $kundli_description[] = "In {$houseNumber} house there " . ($planetCount > 1 ? 'are ' : 'is ') . $planets;
                    } else {
                        $kundli_description[] = "In {$houseNumber} house there is {$planetInfo}";
                    }
                }
            }

            // Add the updated kundli_data and description to the user's data
            $user['kundli_data'] = $houses;
            $user['house_details'] = $kundli_description;
    
            return response()->json([
                'data' => $user,
                'payment_details' => $userPaymentDetails,
                'success' => true
            ]);
        } else {
            // User is not logged in, handle the case accordingly
            return response()->json([
                'message' => 'User is not authenticated',
            ], 401);
        }
    }   

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUser() {
        // Check if the user is logged in
        if (Auth::check()) {
            // Get the currently authenticated user
            $user = Auth::user();
            $user->load('kundli');
                
            // Now $user contains the user details along with the associated kundli details

            $userId = $user->id;

            $userPaymentDetails = Payment::with('user', 'match', 'pandit')->where('user_id', $userId)->get();

            $kundli_data = json_decode($user->kundli, true);
            $kundli_data = json_decode($kundli_data['kundli_data'], true);
            $planetaryData = $kundli_data['output'][1];
    
            // Create an array to represent the 12 houses, initially filled with null values
            $houses = array_fill(0, 12, null);
            $housesDesc = array_fill(0, 12, null);
            
            $houseSigns = [
                'Aries', 'Taurus', 'Gemini', 'Cancer', 'Leo', 'Virgo', 'Libra', 'Scorpio', 'Sagittarius', 'Capricorn', 'Aquarius', 'Pisces'
            ];

            // Iterate through planetary data and map planets to houses
            foreach ($planetaryData as $planetName => $planetInfo) {
                $currentSign = $planetInfo['current_sign'];
    
                // Map the current_sign to the corresponding house number (subtract 1 since arrays are zero-based)
                $houseNumber = $currentSign - 1;
    
                // Initialize the house array if it doesn't exist
                if (!isset($houses[$houseNumber])) {
                    $houses[$houseNumber] = [];
                }

                if (!isset($housesDesc[$houseNumber])) {
                    $housesDesc[$houseNumber] = [];
                }

                $housesDesc[$houseNumber][] = $planetName;
                // Add the first 3 letters of the planet's name to the house's array
                $houses[$houseNumber][] = substr($planetName, 0, 2);
                if(substr($planetName, 0, 2) == 'Mo') {
                    $user['moon_sign'] = $currentSign;
                    $user['sign_name'] = $houseSigns[$currentSign - 1];
                }
            }
    
            // Convert each house's array into a comma-separated string
            foreach ($houses as $houseNumber => $houseArray) {
                if (is_array($houseArray)) {
                    $houses[$houseNumber] = implode(', ', $houseArray);
                }
            }
    
            // Iterate through houses and create descriptions
            $kundli_description = [];

            foreach ($housesDesc as $houseNumber => $planetInfo) {
                $houseNumber++; // Increment by 1 to match house numbering
            
                if ($planetInfo === null) {
                    $kundli_description[] = "In {$houseNumber} house there are no planets";
                } else {
                    if (is_array($planetInfo)) {
                        $planets = implode(' and ', array_filter($planetInfo, 'is_string'));
                        $planetCount = count(array_filter($planetInfo, 'is_string'));
                        $kundli_description[] = "In {$houseNumber} house there " . ($planetCount > 1 ? 'are ' : 'is ') . $planets;
                    } else {
                        $kundli_description[] = "In {$houseNumber} house there is {$planetInfo}";
                    }
                }
            }

            // Add the updated kundli_data and description to the user's data
            $user['kundli_data'] = $houses;
            $user['house_details'] = $kundli_description;
    
            return response()->json([
                'data' => $user,
                'payment_details' => $userPaymentDetails,
                'success' => true
            ]);
        } else {
            // User is not logged in, handle the case accordingly
            return response()->json([
                'message' => 'User is not authenticated',
            ], 401);
        }
    }   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $input = $request->all();
            $user = User::create([
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'email' => $input['email'],
                'password' => bcrypt($input['password']),
                'status' => $input['status'],
                'user_type' => 2
            ]);
            $this->captureLog($user);
            if (isset($user) && !empty($user)) {
                return response()->json([
                    'data' => $user,
                    'success' => true,
                    'msg' => 'User created successfully'
                ], 200);
            }
            return response()->json([
                'data' => [],
                'success' => false,
                'msg' => 'Error creating user'
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            if(!empty($id)){
                $data = User::where('user_type', 2)->with('apiRequest')->where('id', $id)->first();
                if (isset($data) && !empty($data)) {
                    return response()->json([
                        'data' => $data,
                        'success' => true,
                        'msg' => 'Record found'
                    ], 200);
                }else{
                    return response()->json([
                        'data' => [],
                        'success' => false,
                        'msg' => 'No Record found'
                    ], 200);
                }

            }else{
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'msg' => 'Need user id to fetch the data'
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            if(!empty($id)){
                $data = User::where('user_type', 2)->where('id', $id)->first();
                if (isset($data) && !empty($data)) {
                    return response()->json([
                        'data' => $data,
                        'success' => true,
                        'msg' => 'Record found'
                    ], 200);
                }else{
                    return response()->json([
                        'data' => [],
                        'success' => false,
                        'msg' => 'No Record found'
                    ], 200);
                }

            }else{
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'msg' => 'Need user id to fetch the data'
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $input = $request->all();

            $user = User::where('id', $id)->update([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => bcrypt($input['password']),
                'status' => $input['status']
            ]);
            if (isset($user) && !empty($user)) {
                return response()->json([
                    'data' => $user,
                    'success' => true,
                    'msg' => 'User updated successfully'
                ], 200);
            }
            return response()->json([
                'data' => [],
                'success' => false,
                'msg' => 'Error updating user'
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            if(!empty($id)){
                DB::beginTransaction();
                User::findOrFail($id)->delete();
                UserApiRequest::where('user_id',$id)->delete();
                PersonalAccessToken::where('tokenable_id', $id)->delete();
                DB::commit();
                return response()->json([
                    'data' => [],
                    'success' => true,
                    'msg' => 'User deleted successfully'
                ], 200);
            }else{
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'msg' => 'Need user id to delete the record'
                ], 200);
            }
        } catch (\Throwable $th) {
            DB::rollback();
            //throw $th;
        }
    }

    public function createToken($user_id){
        try {
            $user = User::findOrFail($user_id);
            // UserApiRequest::where('user_id',$user_id)->delete();
            PersonalAccessToken::where('tokenable_id', $user_id)->delete();
            $apiToken = $user->createToken('api_token', ['api_access']);
            if($apiToken){
                return response()->json([
                    'data' => $apiToken,
                    'success' => true,
                    'msg' => 'Token created successfully'
                ], 200);
            }
            return response()->json([
                'data' => $apiToken,
                'success' => false,
                'msg' => 'Error creating token'
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

    public function getAllReviews(){
        try {
            $data = Reviews::where('status', 1)->orderBy('created_at', 'desc')->limit(20)->get();
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

    public function submitFeedback(Request $request) {
        try {
            // Create a new review
            Reviews::create([
                'user_id' => $request->input('id'),
                'user_name' => $request->input('name'),
                'review' => $request->input('review'),
                'rating' => $request->input('rating'),
                'status' => $request->input('status', 0),
            ]);

            // You can return the created review or a success message as per your API design
            return response()->json(['status' => true,'message' => 'Thank you for your valuable feedback.'], 200);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['message' => 'An error occurred.'], 200);
        }
    }

    public function getGameZop()
    {
        try {
            $gameJob = GameJob::where('status', 1)->first();

            if ($gameJob) {
                return response()->json(['success' => true, 'data' => ['game_link' => $gameJob->game_link, 'status' => $gameJob->status]]);
            } else {
                return response()->json(['success' => false, 'message' => 'No active game links found.']);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred.', 'error' => $e->getMessage()], 500);
        }
    }

    public function setOnlineVisitors(Request $request) {
        try {
            $ip = $request->ip();

            \Log::info("Hello World!");
            \Log::info($request);
        } catch (\Exception $e) {
            
        }
    }       
}
