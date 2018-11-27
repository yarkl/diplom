<?php

namespace App\Providers;

use App\Services\CourseService;
use App\Services\GraphService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(GraphService::class, function () {
            return new GraphService();
        });
        $this->app->bind(CourseService::class, function () {
            return new CourseService();
        });
    }
}
