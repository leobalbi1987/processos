<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;

class ProcessoMae extends Resource
{
    public static $model = \App\Models\ProcessoMae::class;
    public static $title = 'numero_processo';
    public static $search = ['id'];

    public static function label()
    {
        return 'Processos Mãe';
    }

    public static function singularLabel()
    {
        return 'Processo Mãe';
    }

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            \Laravel\Nova\Fields\Text::make('Número do Processo', 'numero_processo')
                ->rules('required')
                ->sortable(),
            \Laravel\Nova\Fields\Date::make('Validade', 'validade_processo'),
            \Laravel\Nova\Fields\Textarea::make('Objeto'),
            \Laravel\Nova\Fields\BelongsTo::make('Tipo')->display('nome'),
            \Laravel\Nova\Fields\BelongsTo::make('Categoria', 'categoria', \App\Nova\Categoria::class)->display('nome'),
            \Laravel\Nova\Fields\BelongsTo::make('Secretaria', 'secretaria', \App\Nova\Secretaria::class)->display('nome'),
            \Laravel\Nova\Fields\BelongsTo::make('Empresa', 'empresa', \App\Nova\Empresa::class)->display('nome'),
            \Laravel\Nova\Fields\HasMany::make('Processos de Pagamento', 'processosPagamento', \App\Nova\Processo::class),
        ];
    }
}
