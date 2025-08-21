<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\{Club, Field};
use App\Policies\{ClubPolicy, FieldPolicy};

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
        Gate::policy(Field::class, FieldPolicy::class);
        Gate::policy(Club::class, ClubPolicy::class);
    }
}
