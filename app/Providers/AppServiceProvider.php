<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Category;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

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
        Carbon::setLocale(config('app.locale'));
        // ===== View Composer cho layouts.app =====
        View::composer('layouts.app', function ($view) {
            $topCategories = Category::withCount([
                'comics' => fn($q) =>
                $q->where('approval_status', 'approved')
            ])
                ->orderByDesc('comics_count')
                ->limit(5)
                ->get();

            $view->with('topCategories', $topCategories);
        });
    }
}
