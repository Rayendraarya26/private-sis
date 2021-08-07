<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Restriction
{
    public function handle(Request $request, Closure $next)
    {
        if (in_array(request()->route()->getAction()['controller'], session('permission'))) {
            return $next($request);
        } else {
            abort(401);
        }
    }
}
