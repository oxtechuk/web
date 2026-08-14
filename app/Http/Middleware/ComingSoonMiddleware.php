<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ComingSoonMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Skip for CRM admin routes and the coming-soon page itself
        if ($request->is('crm/*') || $request->is('gr-manager-login') || $request->is('coming-soon') || $request->is('erp/*')) {
            return $next($request);
        }

        // Cache the setting for 5 minutes to avoid DB hits on every request
        $comingSoonEnabled = Cache::remember('setting_coming_soon_enabled', 300, function () {
            $setting = Setting::where('key', 'coming_soon_enabled')->first();
            return $setting ? $setting->value : '0';
        });

        if ($comingSoonEnabled == '1' || $comingSoonEnabled === true || $comingSoonEnabled === 1) {
            // Allow lang switcher still
            if ($request->is('lang/*')) {
                return $next($request);
            }
            return redirect()->route('store.coming-soon');
        }

        return $next($request);
    }
}
