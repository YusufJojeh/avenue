<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\URL;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = (string) $request->route('locale');
        abort_unless(in_array($locale, ['ar', 'en'], true), 404);
        app()->setLocale($locale);
        $request->session()->put('locale', $locale);
        URL::defaults(['locale' => $locale]);
        $request->route()->forgetParameter('locale');

        return $next($request);
    }
}
