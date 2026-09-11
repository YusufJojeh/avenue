<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cookie;

class LanguageController extends Controller
{
    /**
     * Switch the application language
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function switch(Request $request): RedirectResponse
    {
        // If some legacy UI still POSTs without a locale, just toggle.
        if (!$request->filled('locale')) {
            return $this->toggle($request);
        }

        $validated = $request->validate([
            'locale' => ['required', 'in:en,ar'],
        ]);
        $locale = $validated['locale'];
        
        // Store locale in session
        session()->put('locale', $locale);
        
        // Also store in cookie for 1 year
        Cookie::queue('locale', $locale, 525600);
        
        // Set locale immediately for this request
        app()->setLocale($locale);
        
        return redirect()->back();
    }

    /**
     * Toggle the application language (GET route to avoid CSRF/session edge cases).
     */
    public function toggle(Request $request): RedirectResponse
    {
        $current = $request->session()->get('locale', app()->getLocale());
        $next = $current === 'ar' ? 'en' : 'ar';

        session()->put('locale', $next);
        Cookie::queue('locale', $next, 525600);
        app()->setLocale($next);

        $previous = url()->previous();
        if (!$previous || str_contains($previous, '/language/')) {
            return redirect()->route('home');
        }

        return redirect()->to($previous);
    }
}
