<?php

namespace App\Providers;

use App\Models\ProjectTask;
use App\Observers\ProjectTaskObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        if (str_contains(request()->getHost(), 'sharedwithexpose.com')) {
            URL::forceScheme('https');
        }

        ProjectTask::observe(ProjectTaskObserver::class);
    }
}
