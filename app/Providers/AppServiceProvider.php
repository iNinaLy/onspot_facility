<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('partials.navbar', function ($view) {
            if (Auth::check()) {
                $unreadNotifications = Auth::user()
                    ->unreadNotifications()
                    ->where('type', 'App\Notifications\ComplaintStatusNotification')
                    ->get();
                
                $view->with('unreadNotifications', $unreadNotifications);
            }
        });
    }

    public function register()
    {
        //
    }
}
