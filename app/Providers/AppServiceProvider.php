<?php

namespace App\Providers;

use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
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
        View::composer('*', function ($view) {

            $currentBranch = null;

            if (Auth::check() && Auth::user()->branch_id) {

                $currentBranch = Branch::with('country')
                    ->find(Auth::user()->branch_id);
            }

            $view->with('currentBranch', $currentBranch);
        });
        Schema::defaultStringLength(191);
    }
}
