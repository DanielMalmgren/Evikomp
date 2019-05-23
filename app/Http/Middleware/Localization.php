<?php

namespace App\Http\Middleware;

use Closure;

class Localization
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
        //logger('Users locale: '.\Auth::user()["locale_id"]);
        if(isset(\Auth::user()["locale_id"])) {
            \App::setLocale(\Auth::user()["locale_id"]);
        }

        return $next($request);
    }
}
