<?php

namespace Modules\Admin\Http\Middleware;

use App\Repositories\User\UserRepositoryInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Gate::allows('admin')) {
            Session::flush();
            Auth::logout();
            return response()->redirectToRoute('login')->withErrors([
                'email' => 'Permission denied.',
            ]);
        }

        return $next($request);
    }
}
