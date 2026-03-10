<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Policies\UserPolicy;
use App\Models\Categoria;
use App\Policies\CategoriaPolicy;
use App\Models\Empresa;
use App\Policies\EmpresaPolicy;
use App\Models\Empenho;
use App\Policies\EmpenhoPolicy;
use App\Models\NotaFiscal;
use App\Policies\NotaFiscalPolicy;
use App\Models\Processo;
use App\Policies\ProcessoPolicy;
use App\Models\ProcessoMae;
use App\Policies\ProcessoMaePolicy;
use App\Models\ProcessoStatusHistorico;
use App\Policies\ProcessoStatusHistoricoPolicy;
use App\Models\Secretaria;
use App\Policies\SecretariaPolicy;
use App\Models\Status;
use App\Policies\StatusPolicy;
use App\Models\Tipo;
use App\Policies\TipoPolicy;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Categoria::class => CategoriaPolicy::class,
        Empresa::class => EmpresaPolicy::class,
        Empenho::class => EmpenhoPolicy::class,
        NotaFiscal::class => NotaFiscalPolicy::class,
        Processo::class => ProcessoPolicy::class,
        ProcessoMae::class => ProcessoMaePolicy::class,
        ProcessoStatusHistorico::class => ProcessoStatusHistoricoPolicy::class,
        Secretaria::class => SecretariaPolicy::class,
        Status::class => StatusPolicy::class,
        Tipo::class => TipoPolicy::class,
    ];

    /**
     * Bootstrap any authentication/authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();


    }
}
