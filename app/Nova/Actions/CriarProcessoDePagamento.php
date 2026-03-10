<?php

namespace App\Nova\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class CriarProcessoDePagamento extends Action
{
    public $name = 'Criar Processo de Pagamento';

    public function handle(ActionFields $fields, Collection $models)
    {
        $notaFiscalId = $models->first()->id;

        // Passamos apenas o ID da nota fiscal via URL, sem tentar o vínculo automático do Nova que está causando conflito
        $url = url("/admin/resources/processos/new?nota_fiscal_id={$notaFiscalId}");

        return Action::redirect($url);
    }

    public function fields(NovaRequest $request)
    {
        return [];
    }
}
