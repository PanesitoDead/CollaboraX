<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
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
       if (env('APP_ENV') === 'production' && env('VIEW_COMPILED_PATH')) {
            $path = env('VIEW_COMPILED_PATH');

            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            View::addNamespace('view', $path);
        }
    }
}
