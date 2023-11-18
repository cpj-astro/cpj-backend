<?php

namespace App\Http\Middleware;

use App\Models\UserApiRequest;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class APIAuthToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->tokenCan('api_access')) {
            return response()->json(['success' => false,'message' => 'Unauthorized'], 401);
        }
        if($request->user()->status === 0){
            return response()->json(['success' => false,'message' => 'Inactive Token'], 403);
        }
        $allowedIps = ['192.168.1.1', '192.168.1.2', '192.168.0.6'];
        $ip = $request->ip();

        if (!in_array($ip, $allowedIps)) {
            // return response()->json(['success' => false,'message' => 'Unauthorized IP address'], 403);
        }

        $user = $request->user();

        UserApiRequest::updateOrCreate(['user_id' => $user->id, 'request_ip' => $ip, 'request_token' => $request->bearerToken()], ['request_counter' => DB::raw('COALESCE(request_counter, 0) + 1')]);
    
        return $next($request);
    }
}
