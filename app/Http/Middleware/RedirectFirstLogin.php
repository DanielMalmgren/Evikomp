<?php

namespace App\Http\Middleware;

use Closure;
use Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectFirstLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  bool  $isfirstlogin
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if((empty(Auth::user()["workplace_id"]) || !Auth::user()->accepted_gdpr || 
            empty(Auth::user()->email) || empty(Auth::user()->title)) &&
            Route::getCurrentRoute()->uri() != 'firstlogin' && !str_starts_with(Route::getCurrentRoute()->uri(), 'settings') &&
            Route::getCurrentRoute()->uri() != 'activetime' && !str_starts_with(Route::getCurrentRoute()->uri(), 'storesettings') &&
            Route::getCurrentRoute()->uri() != 'unsecurelogin' && Route::getCurrentRoute()->uri() != 'login' &&
            Route::getCurrentRoute()->uri() != 'storegdpraccept' && Route::getCurrentRoute()->uri() != 'storefirstloginlanguage') {
            $request->session()->put('orig_requested_url', Route::getCurrentRoute()->uri());
            $request->session()->save();
            usleep(50000);
            return redirect('/firstlogin');
        }

        return $next($request);
    }
}
