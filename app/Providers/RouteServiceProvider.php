<?php

use Illuminate\Support\Facades\Route;


class RouteSrviceProvider extends ServiceProvider
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
    // Route::middleware('web')
    //     ->group(base_path('routes/web.php'));

    Route::middleware('web')
        ->group(base_path('routes/admin.php'));
}
}
