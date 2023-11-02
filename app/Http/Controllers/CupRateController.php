<?php

namespace App\Http\Controllers;

use App\Models\CupRate;
use Illuminate\Http\Request;
use App\Traits\CommonTraits;
use Illuminate\Support\Facades\DB;

class CupRateController extends Controller
{
    //
    public function cupRates()
    {
        try {
            $data = CupRate::where('status', 1)
            ->with('cupRatesTeams')
            ->orderBy('sequence_number', 'asc')
            ->get();
            if($data){
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
        }
        catch (\Throwable $th) {
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
