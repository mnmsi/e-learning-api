<?php

namespace App\Http\Middleware;

use App\Exceptions\Exceptions;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LearnerMiddleware
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
        if (!Gate::allows('learner')) {
            return Exceptions::error("Permission denied!", 422);
        }

        return $next($request);
    }
}
