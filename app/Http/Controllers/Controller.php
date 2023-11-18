<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function terms(){
        return view('terms');
    }

    public function privacy(){
        return view('privacy');
    }

    public function disclaimer(){
        return view('disclaimer');
    }

    public function terms_mango(){
        return view('mango.terms');
    }

    public function privacy_mango(){
        return view('mango.privacy');
    }

    public function disclaimer_mango(){
        return view('mango.disclaimer');
    }
}
