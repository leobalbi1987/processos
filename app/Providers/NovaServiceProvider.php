<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Http\Request;


class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Nova::serving(function () {
            Nova::userMenu(function (Request $request, $menu) {
                // Adiciona o nosso botão de Sair ao menu do Nova
                return $menu->prepend(
                    \Laravel\Nova\Menu\MenuItem::make('Sair', '/logout')
                );
            });
        });

        Nova::footer(function ($request) {
            return Blade::render('
                    Desenvolvido Pela Sec. de Ciência, Tecnologia e Inovação.
            ');
        });

    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->withPasswordResetRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */

protected function gate()
{
    Gate::define('viewNova', function ($user) {
        return $user->hasRole('admin') || $user->hasRole('super-admin');
    });
}
    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            new \App\Nova\Dashboards\Main,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            (new \Sereny\NovaPermissions\NovaPermissions())->canSee(function ($request) {
                return $request->user()->isSuperAdmin();
            }),
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
