<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use App\Traits\CommonTraits;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    use CommonTraits;
    public function __construct()
    {
    }
    public function getNews()
    {
        try {
            $data = News::select('news_id', 'title','description', 'image', 'pub_date', 
            DB::raw("STR_TO_DATE(pub_date, '%d %b, %Y | %h:%i %p') as formatted_pub_date"))
            ->where('status', 1)
            ->orderBy('formatted_pub_date', 'desc')
            ->limit(200)->get();
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
    public function getNewsDetail(Request $request)
    {
        try {
            $news_id = $request->input('news_id');
            $data = News::select('nd.news_id', 'title', 'image','nd.news_content') 
            ->join('news_detail as nd', 'nd.news_id', '=', 'news.news_id')
            ->where('news.news_id', $news_id)
            ->first();
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
