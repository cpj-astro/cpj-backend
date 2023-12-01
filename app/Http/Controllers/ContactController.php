<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function sendMessage(Request $request)
    {
        try {            
            // Send an email
            $emailTo = 'cricketpanditji.astro@gmail.com'; // Replace with your email address

            Mail::send('emails.contactMessage', ['data' => $request], function($message) use($request, $emailTo) {
                $message->from($request->email, $request->name); 
                $message->to($emailTo);
                $message->subject('User Comment');
            });

            // Return a response
            return response()->json([
                'status' => true,
                'message' => 'Message sent successfully.',
            ]);
        } catch (\Throwable $th) {
            // Return a response
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
