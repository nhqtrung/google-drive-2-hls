<?php

namespace App\Http\Middleware;

use Closure;

class AddRangeHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // $request->headers->add('range: bytes=11485616-12485599');
        return $next($request);
    }
}
