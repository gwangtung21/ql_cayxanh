<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Tree;
use App\Models\User;

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
        // Share admin KPIs with admin layout so sidebar shows consistent counts on all admin pages
        View::composer('layouts.admin', function ($view) {
            $view->with('totalTrees', Tree::count());
            $view->with('totalUsers', User::count());
        });
    }
}
