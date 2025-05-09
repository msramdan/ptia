<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

class ViewComposerServiceProvider extends ServiceProvider
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
        View::composer(['users.create', 'users.edit'], function ($view) {
            return $view->with(
                'roles',
                Role::select('id', 'name')->get()
            );
        });
  

		View::composer(['indikator-persepsis.create', 'indikator-persepsis.edit'], function ($view) {
            return $view->with(
                'aspeks',
                \App\Models\Aspek::select('id', 'level')->get()
            );
        });

		View::composer(['bobot-aspeks.create', 'bobot-aspeks.edit'], function ($view) {
            return $view->with(
                'aspeks',
                \App\Models\Aspek::select('id')->get()
            );
        });

	}
}