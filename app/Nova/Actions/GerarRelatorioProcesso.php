<?php

namespace App\Nova\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class GerarRelatorioProcesso extends Action
{
    public $name = 'Gerar Relatório (PDF)';

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        if ($models->count() === 1) {
            // Se apenas um processo for selecionado, gera o relatório detalhado
            return Action::openInNewTab(url("/relatorio/processo/{$models->first()->id}"));
        } else {
            // Se múltiplos processos forem selecionados, gera o relatório em lote
            $processoIds = $models->pluck('id')->toArray();
            $query = http_build_query(['processos' => $processoIds]);
            return Action::openInNewTab(url("/relatorio/processos-lote?{$query}"));
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }
}
