<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request and set application locale.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = ['en', 'bn'];

        $locale = session('locale');

        if (! $locale || ! in_array($locale, $supportedLocales, true)) {
            $locale = config('app.locale', 'bn');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
