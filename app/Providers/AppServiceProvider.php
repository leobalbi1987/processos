<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\AgenciasExternas;
use App\Policies\AgenciasExternasPolicy;
use App\Models\PasseiosTuristicos;
use App\Observers\PasseiosTuristicosObserver;
use App\Models\Organizadores; // Importa o modelo Organizadores
use App\Observers\OrganizadoresObserver; // Importa o Observer OrganizadoresObserver


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
       

    }
}
