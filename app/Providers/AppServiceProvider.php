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
        if (config('app.env') === 'local') {
            // Tenta forçar a desativação da verificação SSL globalmente para o Guzzle
            $proxy = \Illuminate\Support\Facades\Http::getFacadeRoot();
            if (method_exists($proxy, 'withoutVerifying')) {
                \Illuminate\Support\Facades\Http::withoutVerifying();
            }
        }
    }
}
