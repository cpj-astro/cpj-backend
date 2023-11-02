<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\CommonTraits;
use App\Models\Pandits;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PanditController extends Controller
{
    use CommonTraits;

    // Fetch Pandit By Id
    public function getPanditById($id)
    {
        $pandit = Pandits::find($id);

        if (!$pandit) {
            return response()->json([
                'message' => 'Pandit not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pandit,
        ], 200);
    }

    // Fetch all pandits
    public function getAllPandits()
    {
        $pandits = Pandits::all();

        return response()->json([
            'success' => true,
            'pandits' => $pandits,
        ], 200);
    }

    // Create a new pandit
    public function create(Request $request)
    {
        try {
            $input = $request->all();

            $panditData = [
                'name' => $input['name'] ?? '',
                'experience' => $input['experience'] ?? '',
                'rating' => $input['rating'] ?? '',
                'match_astrology_price' => $input['match_astrology_price'] ?? '',
                'description' => $input['description'] ?? '',
            ];
            
            $pandit = Pandits::create($panditData);
            
            if ($file = $request->file('avatar_image')) {
                $path = '/pandits';
                $file_name = date('dmY_His') . '.' . $file->getClientOriginalExtension();
                $file->move(public_path() . $path, $file_name);
                $pandit->avatar_image = url($path . "/" . $file_name);
                $pandit->save();
            }
    
            return response()->json([
                'success' => true,
                'pandit' => $pandit,
                'message' => 'Pandit created successfully',
            ], 201);
        } catch (\Throwable $th) {
            $this->captureExceptionLog($th);
            return response()->json([
                'data' => [],
                'success' => false,
                'message' => 'An error occurred'
            ], 500); // 500 Internal Server Error status code
        }
    }

    // Update an existing pandit
    public function update(Request $request, $id)
    {
        $input = $request->all();

        $existingPlayer = Pandits::findOrFail($id);
        $existingPlayer->update($input);

        if(isset($existingPlayer) && !empty($existingPlayer->avatar_image)){
            $oldMedia = substr($existingPlayer->avatar_image, strrpos($existingPlayer->avatar_image ,"/") + 1);
            $path = '/pandits/';

            $oldMediaPath = public_path() . $path . $oldMedia;

            if (File::exists($oldMediaPath)) {
                unlink($oldMediaPath);
            }
        }

        if ($file = $request->file('avatar_image')) {
            $path = '/pandits';
            $file_name = date('dmY_His') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path() . $path, $file_name);
            $existingPlayer->avatar_image = url($path . "/" . $file_name);
            $existingPlayer->save();
        }

        return response()->json([
            'success' => true,
            'data' => $existingPlayer,
            'message' => 'Pandit updated successfully',
        ], 200);
    }

    // Delete a pandit
    public function delete($id)
    {
        $pandit = Pandits::findOrFail($id);
        $pandit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pandit deleted successfully',
        ], 200);
    }
}
