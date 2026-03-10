<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Http\Requests\NovaRequest;

class Empenho extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Empenho>
     */
    public static $model = \App\Models\Empenho::class;

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
        'numero_empenho',
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
            Text::make('Número do Empenho', 'numero_empenho')
                ->rules('required'),
            Currency::make('Valor Global', 'valor_global')
                ->currency('BRL'),
            Currency::make('Saldo', 'saldo')
                ->currency('BRL')
                ->readonly(),

            BelongsTo::make('Processo', 'processo', \App\Nova\Processo::class)
                ->display('numero_processo')
                ->exceptOnForms(),

            BelongsTo::make('Empresa')->display('nome')
                ->nullable()
                ->showCreateRelationButton(),

            BelongsTo::make('Secretaria', 'secretaria', \App\Nova\Secretaria::class)->display('nome')->rules('required')
                ->showCreateRelationButton(),

            HasMany::make('Notas Fiscais', 'notasFiscais', \App\Nova\NotaFiscal::class),
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
        return [
            new \App\Nova\Filters\EmpenhoProcesso,
        ];
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
        return [];
    }
}
