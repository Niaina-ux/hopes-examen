<?php

namespace App\Providers;

use App\Models\Categorie;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer('layouts.prof-layouts.proflayoutshead', function ($view) {
            $slug = request()->route('slug');

            $categorie = $slug
                ? Categorie::where('slug', $slug)->first()
                : null;

            $typePremier = $categorie
                ? $categorie->typesExerciceAutorises()->first()
                : null;

            $view->with([
                'categorie' => $categorie,
                'typePremier' => $typePremier,
            ]);
        });
    }
}
