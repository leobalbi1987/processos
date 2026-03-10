<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Http\Requests\NovaRequest;

class ProcessoStatusHistorico extends Resource
{
    public static $model = \App\Models\ProcessoStatusHistorico::class;
    public static $title = 'id';
    public static $search = ['id'];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Processo'),
            BelongsTo::make('Status')->display('nome'),
            BelongsTo::make('User')->display('name'),
            Textarea::make('Observacao'),
            DateTime::make('created_at', 'created_at')->onlyOnDetail(),
        ];
    }
}