<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Services\SupabaseService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        View::composer('layouts.navigation', function ($view) {
            $unreadNotifications = collect(); // Default empty collection

            if (Auth::check()) {
                $unreadNotifications = Auth::user()
                    ->unreadNotifications()
                    ->where('type', \App\Notifications\ComplaintNotification::class)
                    ->get();
            }

            $view->with('unreadNotifications', $unreadNotifications);
        });
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(SupabaseService::class, function ($app) {
            return new SupabaseService();
        });
    }
}