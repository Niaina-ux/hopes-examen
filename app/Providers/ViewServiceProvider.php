<?php

namespace App\Providers;

use App\Models\Categorie;
use App\Models\Prof;
use App\Models\StudentExamen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
       View::composer('*', function ($view) {
            // Categorie rehetra ho an'ny navbar (admin)
            $view->with('navCategories', Categorie::all());

            // Categorie an'ny prof login (raha misy)
            if (Auth::check() && Auth::user()->role === 'prof') {
                $prof = Prof::where('user_id', Auth::id())->with('categorie')->first();
                $view->with('profCategorie', $prof?->categorie);
            }
        });
    }
}
