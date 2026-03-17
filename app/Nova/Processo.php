<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Http\Requests\NovaRequest;

class Processo extends Resource
{
    public static function label()
    {
        return 'Processos de Pagamento';
    }

    public static function singularLabel()
    {
        return 'Processo de Pagamento';
    }
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Processo>
     */
    public static $model = \App\Models\Processo::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'numero_processo';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'numero_processo',
    ];

    public static $with = ['notaFiscal', 'empresa', 'secretaria'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Text::make('Nº da Nota Fiscal', 'nota_fiscal_numero')
                ->onlyOnForms()
                ->help('Digite o número da nota fiscal para auto preencher os dados do processo')
                ->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
                    return [];
                }),

            ID::make()->sortable(),

            BelongsTo::make('Empresa', 'empresa', \App\Nova\Empresa::class)
                ->display('nome')
                ->sortable()
                ->default(function ($request) {
                    $notaFiscalId = $request->nota_fiscal_id ?? request('nota_fiscal_id');
                    if ($notaFiscalId) {
                        $nota = \App\Models\NotaFiscal::find($notaFiscalId);
                        return $nota?->empenho?->empresa_id;
                    }
                    return null;
                })
                ->dependsOn(['empenho_numero', 'nota_fiscal_numero'], function (BelongsTo $field, NovaRequest $request, FormData $formData) {
                    if ($formData->nota_fiscal_numero) {
                        $nota = \App\Models\NotaFiscal::where('numero_nf', $formData->nota_fiscal_numero)->first();
                        if ($nota && $nota->empenho) {
                            $field->default($nota->empenho->empresa_id);
                        }
                    } elseif ($formData->empenho_numero) {
                        $empenho = \App\Models\Empenho::where('numero_empenho', $formData->empenho_numero)->first();
                        if ($empenho) {
                            $field->default($empenho->empresa_id);
                        }
                    }
                }),

            Text::make('Número do Processo', 'numero_processo')
                ->rules('required')
                ->sortable()
                ->asHtml()
                ->displayUsing(function ($value, $resource) {
                    $url = url('/admin/resources/empenhos?empenhos_filter=' . base64_encode(json_encode([
                        ['class' => \App\Nova\Filters\EmpenhoProcesso::class, 'value' => $resource->id]
                    ])));
                    return "<a href=\"$url\" class=\"link-default\">$value</a>";
                }),

            Text::make('Nº da Nota Fiscal', 'notaFiscal.numero_nf')
                ->exceptOnForms(),

            Text::make('Mês de Referência', 'notaFiscal.data_referencia')
                ->exceptOnForms(),

            \Laravel\Nova\Fields\Currency::make('Valor', 'notaFiscal.valor_nf')
                ->currency('BRL')
                ->exceptOnForms(),

            Text::make('Empenho (Número)', 'empenho_numero')
                ->onlyOnForms()
                ->help('Digite o número do empenho para auto preencher Empresa e Secretaria')
                ->default(function ($request) {
                    $notaFiscalId = $request->nota_fiscal_id ?? request('nota_fiscal_id');
                    if ($notaFiscalId) {
                        $nota = \App\Models\NotaFiscal::find($notaFiscalId);
                        return $nota?->empenho?->numero_empenho;
                    }
                    return null;
                })
                ->dependsOn(['nota_fiscal_numero'], function (Text $field, NovaRequest $request, FormData $formData) {
                    if ($formData->nota_fiscal_numero) {
                        $nota = \App\Models\NotaFiscal::where('numero_nf', $formData->nota_fiscal_numero)->first();
                        if ($nota && $nota->empenho) {
                            $field->default($nota->empenho->numero_empenho);
                        }
                    }
                })
                ->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
                    return [];
                }),
            Date::make('Validade', 'validade_processo')->hideFromIndex(),
            Textarea::make('Objeto')
                ->default(function ($request) {
                    $notaFiscalId = $request->nota_fiscal_id ?? request('nota_fiscal_id');
                    if ($notaFiscalId) {
                        $nota = \App\Models\NotaFiscal::find($notaFiscalId);
                        if ($nota) {
                            $mes = $nota->data_referencia ? " - Mês: {$nota->data_referencia}" : "";
                            return "Pagamento referente à Nota Fiscal nº {$nota->numero_nf}{$mes}";
                        }
                    }
                    return null;
                })
                ->dependsOn(['nota_fiscal_numero'], function (Textarea $field, NovaRequest $request, FormData $formData) {
                    if ($formData->nota_fiscal_numero) {
                        $nota = \App\Models\NotaFiscal::where('numero_nf', $formData->nota_fiscal_numero)->first();
                        if ($nota) {
                            $mes = $nota->data_referencia ? " - Mês: {$nota->data_referencia}" : "";
                            $field->default("Pagamento referente à Nota Fiscal nº {$nota->numero_nf}{$mes}");
                        }
                    }
                }),
            BelongsTo::make('Tipo')
                ->display('nome')->hideFromIndex()
                ->default(1),
            BelongsTo::make('Categoria', 'categoria', \App\Nova\Categoria::class)
                ->display('nome')->hideFromIndex(),
            BelongsTo::make('Status', 'status', \App\Nova\Status::class)
                ->display('nome')
                ->sortable(),
            BelongsTo::make('Processo Mãe', 'processoMae', \App\Nova\ProcessoMae::class)
                ->display('numero_processo')
                ->sortable()
                ->nullable(),

            BelongsTo::make('Secretaria', 'secretaria', \App\Nova\Secretaria::class)
                ->display('nome')
                ->sortable()
                ->default(function ($request) {
                    $notaFiscalId = $request->nota_fiscal_id ?? request('nota_fiscal_id');
                    if ($notaFiscalId) {
                        $nota = \App\Models\NotaFiscal::find($notaFiscalId);
                        return $nota?->empenho?->secretaria_id;
                    }
                    return null;
                })
                ->dependsOn(['empenho_numero', 'nota_fiscal_numero'], function (BelongsTo $field, NovaRequest $request, FormData $formData) {
                    if ($formData->nota_fiscal_numero) {
                        $nota = \App\Models\NotaFiscal::where('numero_nf', $formData->nota_fiscal_numero)->first();
                        if ($nota && $nota->empenho) {
                            $field->default($nota->empenho->secretaria_id);
                        }
                    } elseif ($formData->empenho_numero) {
                        $empenho = \App\Models\Empenho::where('numero_empenho', $formData->empenho_numero)->first();
                        if ($empenho) {
                            $field->default($empenho->secretaria_id);
                        }
                    }
                }),
            Select::make('Localização', 'localizacao')
                ->options([
                    'financas' => 'Finanças',
                    'tecnologia' => 'Tecnologia',
                    'fazenda' => 'Fazenda',
                ])
                ->rules('required')
                ->onlyOnForms(),
            Text::make('Dias', 'dias')->onlyOnForms(),
            \Laravel\Nova\Fields\HasOne::make('Nota Fiscal', 'notaFiscal', \App\Nova\NotaFiscal::class),
            HasMany::make('Processos Status Historico', 'statusHistoricos', \App\Nova\ProcessoStatusHistorico::class),
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
            new \App\Nova\Actions\GerarRelatorioProcesso,
        ];
    }
}
