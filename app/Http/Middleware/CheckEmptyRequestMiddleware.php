<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Exceptions\Exceptions;

class CheckEmptyRequestMiddleware
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
        if (count(array_filter($request->all())) == 0) {
            return Exceptions::error("Empty request!");
        }

        return $next($request);
    }
}
