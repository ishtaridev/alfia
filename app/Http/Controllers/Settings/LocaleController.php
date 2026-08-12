<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    private const SUPPORTED_LOCALES = ['en', 'ar', 'fr'];

    public function update(Request $request): JsonResponse
    {
        $locale = $request->input('locale');

        if (in_array($locale, self::SUPPORTED_LOCALES, true)) {
            Session::put('locale', $locale);
            app()->setLocale($locale);

            return response()->json(['locale' => $locale]);
        }

        return response()->json(['error' => 'Unsupported locale'], 422);
    }
}
