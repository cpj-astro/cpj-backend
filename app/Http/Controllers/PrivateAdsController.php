<?php

namespace App\Http\Controllers;

use App\Models\PrivateAds;
use Illuminate\Http\Request;
use App\Traits\CommonTraits;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class PrivateAdsController extends Controller
{
    use CommonTraits;
    public function getAllPrivateAds(){
        try {
            $data = PrivateAds::get();
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
}
