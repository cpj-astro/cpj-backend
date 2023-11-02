<?php

namespace App\Http\Controllers\Admin;

use App\Models\PrivateAds;
use Illuminate\Http\Request;
use App\Traits\CommonTraits;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class PrivateAdsController extends Controller
{
    use CommonTraits;
    public function getList($user_id){
        try {
            if(!empty($user_id)){
                $data = PrivateAds::where('user_id', $user_id)->get();
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
            }else{
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'msg' => 'Invalid user id'
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

    public function getAd($id){
        try {
            $data = PrivateAds::find($id);
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

    public function addNewAd(Request $request){
        try {
            $input = $request->all();
            $rules = [
                'title' => 'required',
                'ad_type' => 'required',
                'status' => 'required',
                'link' => 'required',
                'category' => 'required',
                'expiry_date' => 'required',
                'user_id' => 'required',
            ];
            $messages = [
                'title.required' => 'Title is mandatory',
                'ad_type.required' => 'Ad type is mandatory',
                'status.required' => 'Status is mandatory',
                'link.required' => 'Link is mandatory',
                'category.required' => 'category is mandatory',
                'expiry_date.required' => 'Expiry Date is mandatory',
                'user_id.required' => 'User id is mandatory',
            ];

            $validator = Validator::make($input, $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'msg' => $validator
                ], 200);
            }
            $pd = new PrivateAds();
            $pd->title = $input['title'];
            $pd->ad_type = $input['ad_type'];
            $pd->status = $input['status'];
            $pd->link = $input['link'];
            $pd->category = $input['category'];
            $pd->expiry_date = $input['expiry_date'];
            $pd->user_id = $input['user_id'];
            $pd->save();
            if ($file = $request->file('file')) {
                $path = '/private_ads';
                // $name = $file->getClientOriginalName();

                $file_name = date('dmY_His') . '.' . $file->getClientOriginalExtension();
                $file->move(public_path() . $path, $file_name);

                $pd->media_file = url($path . "/" . $file_name);
                $pd->save();
            }
            return response()->json([
                'data' => [],
                'success' => true,
                'msg' => 'Ad successfully inserted'
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

    public function deleteAd($id){
        try {
            $pd = PrivateAds::findOrFail($id);
            $pdFile =  $pd->media_file;

            
            $deletSql = $pd->delete();
            if ($deletSql) {
                $pdFile = substr($pdFile, strrpos($pdFile ,"/") + 1);

                $path = '/private_ads/';

                $old_image_path = public_path() . $path . $pdFile;
                if (File::exists($old_image_path)) {
                    //File::delete($image_path);
                    unlink($old_image_path);
                }
            }

            return response()->json([
                'data' => [],
                'success' => true,
                'msg' => 'Ad successfully removed'
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

    public function updateAd($id, Request $request){
        try {
            $input = $request->all();
            $pd = PrivateAds::findOrFail($id);
            $oldMedia = $pd->media_file;
            $pd->title = $input['title'];
            $pd->ad_type = $input['ad_type'];
            $pd->status = $input['status'];
            $pd->link = $input['link'];
            $pd->category = $input['category'];
            $pd->expiry_date = $input['expiry_date'];
            $pd->save();

            if ($file = $request->file('file')) {
                $path = '/private_ads';
                // $name = $file->getClientOriginalName();

                $file_name = date('dmY_His') . '.' . $file->getClientOriginalExtension();
                $file->move(public_path() . $path, $file_name);

                $pd->media_file = url($path . "/" . $file_name);
                $pd->save();

                if(isset($oldMedia) && !empty($oldMedia)){
                    $oldMedia = substr($oldMedia, strrpos($oldMedia ,"/") + 1);
                    $path = '/private_ads/';
    
                    $oldMediaPath = public_path() . $path . $oldMedia;
    
                    if (File::exists($oldMediaPath)) {
                        //File::delete($image_path);
                        unlink($oldMediaPath);
                    }
                }
            }
            return response()->json([
                'data' => [],
                'success' => true,
                'msg' => 'Ad successfully updated'
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
