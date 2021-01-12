<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
class CheckApproval
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
        $approved = 0;
        if (Auth::check()) {
            $approved = Auth::user()->approved;            
        }

        if(!$approved){
            return redirect('/approve');
        }
        return $next($request);
    }
}
