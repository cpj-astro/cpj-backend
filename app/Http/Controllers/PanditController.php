<?php

namespace App\Http\Controllers;
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

    // Fetch all pandits
    public function getAllPandits()
    {
        $pandits = Pandits::all();

        return response()->json([
            'success' => true,
            'data' => $pandits,
        ], 200);
    }
}
