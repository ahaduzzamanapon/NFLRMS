<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Switch application language between English and Bangla.
     */
    public function switch(string $locale, Request $request): RedirectResponse
    {
        if (in_array($locale, ['en', 'bn'], true)) {
            session(['locale' => $locale]);
        }

        return redirect()->back();
    }
}
