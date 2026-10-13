<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('Accept-Language', config('app.locale'));

        // اللغات المسموح بها
        if (! in_array($locale, ['ar', 'en'])) {
            $locale = config('app.locale');
        }
        

        App::setLocale($locale);

       return $next($request);

    }
}
