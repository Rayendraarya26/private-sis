<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Restriction
{
    public function handle(Request $request, Closure $next)
    {
        $currentController = request()->route()->getAction()['controller'];
        $availController = session('permission');
        if (in_array($currentController, $availController)) {
            return $next($request);
        } else {
            abort(401);
        }
    }
}
