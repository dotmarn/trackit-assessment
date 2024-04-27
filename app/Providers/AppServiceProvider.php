<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

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
        Response::macro('apiSuccess', function($status, $message, $data = []) {
            return Response::json(['status' => true, 'message' => $message, 'data' => $data], $status);
        });

        Response::macro('apiError', function($status, $message, $error = []) {
            return Response::json(['status' => false, 'message' => $message, 'error' => $error], $status);
        });
    }
}
