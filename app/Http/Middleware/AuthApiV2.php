<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthApiV2
{   
    const X_API_TOKEN = 'X-API-TOKEN';
    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasHeader(self::X_API_TOKEN)) {
            return response()->json(['error' => 'api token is required'], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::where('user_token', $request->header(self::X_API_TOKEN))->first();
        if (!$user) {
            return response()->json(['error' => 'user not found'], Response::HTTP_UNAUTHORIZED);
        }
        
        Auth::login($user);
        
        return $next($request);
    }

}
