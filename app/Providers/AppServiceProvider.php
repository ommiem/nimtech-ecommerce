<?php

namespace App\Providers;

use App\Models\County;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production or when app URL is https
        try {
            $appUrl = config('app.url');
        } catch (\Throwable $e) {
            $appUrl = null;
        }
        if (app()->environment('production') || (is_string($appUrl) && str_starts_with($appUrl, 'https://')) || (bool) env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }

        // Compatibility for older MySQL/MariaDB index length limits on utf8mb4.
        Schema::defaultStringLength(191);

        $host = '';
        try {
            $host = strtolower((string) request()->getHost());
        } catch (\Throwable $e) {
            $host = '';
        }

        $theme = null;
        if (
            $host === 'beauty360.co.ke' ||
            $host === 'www.beauty360.co.ke' ||
            str_ends_with($host, '.beauty360.co.ke')
        ) {
            $theme = 'beauty360';
        }

        try {
            $theme ??= optional(\App\Models\Setting::getCached())->active_theme ?: 'nimtech';
        } catch (\Throwable $e) {
            $theme ??= 'nimtech';
        }
        \View::addNamespace('theme', [
            resource_path('views/themes/'.$theme),
            resource_path('views'),
        ]);

        // Let anonymous Blade components be overridden per theme without changing tags
        $themeComponents = resource_path('views/themes/'.$theme.'/components');
        if (is_dir($themeComponents)) {
            Blade::anonymousComponentPath($themeComponents); // No prefix so <x-...> will resolve here first
        }
        // Ensure default components also registered
        Blade::anonymousComponentPath(resource_path('views/components'));

        View::composer([
            'account.profile.index',
            'account.addresses.create',
            'account.addresses.edit',
            'admin.users.create',
            'admin.users.edit',
        ], function ($view) {
            static $cachedCounties = null;

            if ($cachedCounties === null) {
                $cachedCounties = collect();
                try {
                    if (Schema::hasTable('counties')) {
                        $cachedCounties = County::query()
                            ->orderBy('sort_order')
                            ->orderBy('name')
                            ->pluck('name');
                    }
                } catch (\Throwable $e) {
                    $cachedCounties = collect();
                }
            }

            $view->with('counties', $cachedCounties);
        });
    }
}
