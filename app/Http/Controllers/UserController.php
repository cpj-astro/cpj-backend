<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccessToken;
use App\Models\User;
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
        try{
            $data = User::with('kundli')->where('user_type', 1)->get();
            foreach ($data as $user) {
                $kundli_data = json_decode($user->kundli, true);
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
    
                    // Add the planet's name to the house's array
                    $houses[$houseNumber][] = $planetName;
                }
                $user['kundli_data'] = $houses;
            }
            
            if (isset($data) && !empty($data)) {
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

            // You can return the user's information as needed
            return response()->json([
                'data' => $user,
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
}
