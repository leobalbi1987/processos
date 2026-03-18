<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;

class NotaFiscal extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\NotaFiscal>
     */
    public static $model = \App\Models\NotaFiscal::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            \Laravel\Nova\Fields\Text::make('Número NF', 'numero_nf')
                ->rules('required'),
            \Laravel\Nova\Fields\Text::make('Ordem de Serviço', 'ordem_de_servico'),
            \Laravel\Nova\Fields\Currency::make('Valor', 'valor_nf')
                ->currency('BRL'),
            \Laravel\Nova\Fields\Date::make('Data Emissão', 'data_emissao'),
            Select::make('Mês de Referência', 'data_referencia')->options([
                'Janeiro' => 'Janeiro',
                'Fevereiro' => 'Fevereiro',
                'Março' => 'Março',
                'Abril' => 'Abril',
                'Maio' => 'Maio',
                'Junho' => 'Junho',
                'Julho' => 'Julho',
                'Agosto' => 'Agosto',
                'Setembro' => 'Setembro',
                'Outubro' => 'Outubro',
                'Novembro' => 'Novembro',
                'Dezembro' => 'Dezembro',
            ])->displayUsingLabels(),
            BelongsTo::make('Empenho Principal', 'empenho', Empenho::class)
                ->display('numero_empenho')
                ->rules('required'),

            BelongsTo::make('Empenho Extra 1', 'empenhoExtra1', Empenho::class)
                ->display('numero_empenho')
                ->onlyOnForms()
                ->nullable()
                ->help('Adicione se o saldo do empenho principal for insuficiente para cobrir o valor total.'),

            BelongsTo::make('Empenho Extra 2', 'empenhoExtra2', Empenho::class)
                ->display('numero_empenho')
                ->onlyOnForms()
                ->nullable()
                ->help('Adicione se os empenhos anteriores forem insuficientes.'),

            \Laravel\Nova\Fields\BelongsTo::make('Processo de Pagamento', 'processo', \App\Nova\Processo::class)
                ->display('numero_processo')
                ->nullable(),

            HasMany::make('Processos de Pagamento', 'processosPagamento', \App\Nova\Processo::class),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            new \App\Nova\Actions\CriarProcessoDePagamento,
        ];
    }
}
