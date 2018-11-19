<?php

namespace App\Http\Middleware;

use Closure;

class ChangeLocale
{
    /**
     * Handle an incoming request.
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $acceptLanguage = $request->header('accept-language');
        if ($acceptLanguage) {
            $language = current(explode(',', $acceptLanguage));
            app('translator')->setLocale($language);
        }

        return $next($request);
    }
}
