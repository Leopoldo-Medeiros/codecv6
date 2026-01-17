<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Using a closure based composer...
        View::composer('includes.admin.menu.navbar', function ($view) {
            $view->with('user', Auth::user());
        });
    }

    public function register()
    {
        //
    }
}
