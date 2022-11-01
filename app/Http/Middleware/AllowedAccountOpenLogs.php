<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AllowedAccountOpenLogs
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
        $availableEmail = ['dolkodesolutions@gmail.com', 'aldinokemal2104@gmail.com', 'saiful.mmuttaqin@gmail.com'];
        if (auth()->check() && in_array(auth()->user()->user_email, $availableEmail)) {
            return $next($request);
        }
        abort(404);
    }
}
