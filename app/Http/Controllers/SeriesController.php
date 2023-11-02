<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\CommonTraits;
use App\Models\Series;

class SeriesController extends Controller
{
    use CommonTraits;
    public function __construct()
    {
    }

    public function fetchSeriesData(Request $request) {
        try {
            $data = Series::select('series_id','series_name','series_date','total_matches','start_date','end_date','image','month_wise')
                ->where('series_id', $request[0])->first();
            return response()->json([
                'data' => $data,
                'success' => true,
                'msg' => 'Data found'
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

    public function getList(){
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

    public function getPointsTable(Request $request){
        try {
            $apiUrl = config('services.cricket-champion.endpoint') . 'pointsTable/' . config('services.cricket-champion.token');
            $series_id = $request->input('series_id');
            if (isset($series_id) && !empty($series_id)) {
                $postData = [
                    'series_id' => $series_id
                ];
                $res = $this->pullData($apiUrl, 'POST', $postData);

                $res = json_decode($res, true);

                if ($res['status']) {
                    return response()->json([
                        'data' => $res['data'],
                        'success' => true,
                        'msg' => 'Data found'
                    ], 200);
                } else {
                    return response()->json([
                        'data' => [],
                        'success' => false,
                        'msg' => (isset($res['msg']) ? $res['msg'] : 'No match id')
                    ], 200);
                }
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

}
