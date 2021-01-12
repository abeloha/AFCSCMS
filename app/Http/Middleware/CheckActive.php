<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $deactivated = 0;
        if (Auth::check()) {
            $deactivated = Auth::user()->deactivated;            
        }

        if($deactivated){
            return redirect('/deactivate');
        }
        return $next($request);
    }
}
