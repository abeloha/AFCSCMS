<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HasAbility
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $action)
    {
        if(!check_has_ability($action)){
            return redirect("/?unauthorised=failed_test_for_$action");
        }
        return $next($request);
    }
}
