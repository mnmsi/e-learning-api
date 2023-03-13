<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Exceptions\Exceptions;
use Illuminate\Support\Facades\Gate;

class EducatorMiddleware
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
        if (!Gate::allows('educator')) {
            return Exceptions::error("Permission denied!", 422);
        }

        return $next($request);
    }
}
