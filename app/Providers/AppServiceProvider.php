<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MattDaneshvar\Survey\Contracts\Question;
use MattDaneshvar\Survey\Contracts\Survey;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(Survey::class, \App\Models\Survey::class);
        $this->app->bind(Question::class, \App\Models\Question::class);
        $this->app->bind(Question::class, \App\Models\Question::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
