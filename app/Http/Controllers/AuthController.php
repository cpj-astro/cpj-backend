<?php

namespace App\Http\Controllers;

use App\Models\PrivateAds;
use App\Models\Setting;
use App\Models\User;
use App\Models\Kundli;
use App\Models\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Traits\CommonTraits;
use Carbon\Carbon;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Mail; 
use DB; 

class AuthController extends Controller
{
    use CommonTraits;
    public function signIn(Request $request)
    {
        try {
            // Attempt to log in the user
            if (auth()->attempt(['email' => $request->email, 'password' => $request->password, 'status' => 1, 'user_type' => 1])) {
                $user = auth()->user();
                $token = $user->createToken('client_token', ['client-access'], config('session.lifetime'));
                // ->plainTextToken; try later with this token method
                return response(['status' => true, 'message' => 'Login successful', 'user' => $user, 'token' => $token]);
            } else {
                return response(['status' => false, 'message' => 'Invalid credentials'], 200);
            }
        } catch (\Throwable $th) {
            // If authentication fails, return an error response
            return response(['status' => false, 'message' => 'Invalid credentials'], 200);
        }
    }


    public function signUp(Request $request)
    {
        try {
            
            // Create a new user
            $user = User::create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'password' => Hash::make($request->input('password')),
                'birth_date' => $request->input('birth_date'),
                'birth_time' => $request->input('birth_time'),
                'birth_place' => $request->input('birth_place'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'user_type' => 1,
            ]);
            
            // Creating date time object
            $dateObject = strtotime($request->input('birth_date'));
            $timeObject = strtotime($request->input('birth_time'));
            
            // Extracting the year, month, and date from the date & time objects
            $date = date('d', $dateObject);
            $month = date('m', $dateObject);
            $year = date('Y', $dateObject);
            $hour = date('H', $timeObject);
            $minute = date('i', $timeObject);

            if($user) {
                // Prepare the data array
                $postData = array(
                    "year" => intval($year),
                    "month" => intval($month),
                    "date" => intval($date),
                    "hours" => intval($hour),
                    "minutes" => intval($minute),
                    "seconds" => 0,
                    "latitude" => $user->latitude,
                    "longitude" => $user->longitude,
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

                $kundliRecord = Kundli::where('user_id', $user->id)->first();
                $newKundliRecord = null;
                if ($kundliRecord) {
                    // Delete the existing kundli record
                    $kundliRecord->delete();
                } else {
                    $newKundliRecord = Kundli::create([
                        'user_id' => $user->id,
                        'player_id' => null,
                        'kundli_data' => $response
                    ]);
                }
            }
    
            return response()->json(['status' => true, 'Account Created Successfully'], 200);
        } catch (\Throwable $th) {
            $this->captureExceptionLog($th);
            return response()->json([
                'data' => [],
                'success' => false,
                'msg' => $th->getMessage()
            ], 200);
        }
    }
    
    public function sendFPLink(Request $request) {
        try {
            $token = Str::random(64);
            
            $verificationLink = env('EMAIL_URL') . 'reset-password/' . $token;

            Mail::send('emails.forgetPassword', ['verificationLink' => $verificationLink], function($message) use($request) {
                $message->from('cricketpanditji.astro@gmail.com', 'CricketPanditJi'); 
                $message->to($request->email);
                $message->subject('Reset Password');
            });

            DB::table('password_resets')
            ->updateOrInsert(
                ['email' => $request->email],
                [
                    'token' => $token,
                    'created_at' => now(),
                ]
            );

            return response()->json(['status' => true, 'message' => 'We have e-mailed your password reset link!'], 200);
        } catch (\Throwable $th) {
            $this->captureExceptionLog($th);
            return response()->json([
                'data' => [],
                'success' => false,
                'msg' => $th->getMessage()
            ], 200);
        }
    }

    public function resetPassword(Request $request) {
        try {
            $updatePassword = DB::table('password_resets')->where([
              'token' => $request->token
            ])->first();

            if(!$updatePassword){
                return response()->json(['status' => false, 'message' => 'Invalid Token.'], 200);
            }

            $user = DB::table('password_resets')->where(['token' => $request->token])->first();
            
            if($user) {
                User::where('email', $user->email)->update(['password' => Hash::make($request->new_password)]);
            }

            DB::table('password_resets')->where(['token'=> $request->token])->delete();

            return response()->json(['status' => true, 'message' => 'Password changed successfully, Please try to Sign In.'], 200);
        } catch (\Throwable $th) {
            $this->captureExceptionLog($th);
            return response()->json([
                'data' => [],
                'status' => false,
                'message' => $th->getMessage()
            ], 200);
        }
    }

    public function me(Request $request)
    {
        $token = auth()->user()->currentAccessToken();

        return $token;
        // return $request->user()->token()->expires_at;
    }

    public function adminLogin(Request $request)
    {
        if (!Auth::attempt((['email' => $request['email'], 'password' => $request['password'], 'status' => 1, 'user_type' => 0]))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('admin_token', ['*'], Carbon::now()->addDay());

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
        
    }
}
