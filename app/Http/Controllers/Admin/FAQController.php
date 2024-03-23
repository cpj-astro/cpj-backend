<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\CommonTraits;
use App\Models\Faq;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FAQController extends Controller
{
    use CommonTraits;

    // Fetch Faq By Id
    public function getFaqById($id)
    {
        $faq = Faq::find($id);

        if (!$faq) {
            return response()->json([
                'message' => 'Faq not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $faq,
        ], 200);
    }

    // Fetch all faqs
    public function getAllFaq()
    {
        $faq = Faq::all();

        return response()->json([
            'success' => true,
            'faqs' => $faq,
        ], 200);
    }

    // Create a new faq
    public function create(Request $request)
    {
        try {
            $input = $request->all();

            $faqData = [
                'title' => $input['title'] ?? '',
                'value' => $input['value'] ?? '',
                'status' => $input['status'] ?? ''
            ];
            
            $faq = Faq::create($faqData);
           
            return response()->json([
                'success' => true,
                'faq' => $faq,
                'message' => 'Faq created successfully',
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

        $existingPlayer = Faq::findOrFail($id);
        $existingPlayer->update($input);

        return response()->json([
            'success' => true,
            'data' => $existingPlayer,
            'message' => 'Faq updated successfully',
        ], 200);
    }

    // Delete a pandit
    public function delete($id)
    {
        $pandit = Faq::findOrFail($id);
        $pandit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Faq deleted successfully',
        ], 200);
    }
}
