<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Processo de Pagamento - Detalhado</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #000; line-height: 1.3; }

        .header-container { width: 100%; margin-bottom: 20px; position: relative; height: 80px; }
        .logo-left { position: absolute; left: 0; top: 0; height: 80px; width: auto; }
        .logo-right { position: absolute; right: 0; top: 0; height: 80px; width: auto; }
        .header-text { text-align: center; font-weight: bold; padding-top: 10px; }
        .header-text div { margin-bottom: 2px; }
        .state-text { font-size: 14px; }
        .city-text { font-size: 13px; }
        .dept-text { font-size: 12px; }

        .report-title { text-align: center; font-weight: bold; font-size: 12px; margin: 15px 0; border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 5px 0; }
        .update-date { text-align: right; font-weight: bold; margin-bottom: 10px; }

        .section-title { font-size: 12px; font-weight: bold; background-color: #f2f2f2; padding: 5px; margin-top: 20px; margin-bottom: 10px; border-left: 5px solid #000; border-top: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; }

        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-table th, .info-table td { border: 1px solid #000; padding: 6px 8px; text-align: left; }
        .info-table th { background-color: #fafafa; width: 25%; font-weight: bold; }

        .objeto { white-space: pre-wrap; background: #fff; padding: 10px; border: 1px solid #000; min-height: 50px; }

        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 8px; border-top: 0.5px solid #ccc; padding-top: 3px; }
    </style>
</head>
<body>
    <div class="header-container">
        <img src="{{ $logo }}" class="logo-left">
        <img src="{{ $logo_right }}" class="logo-right">
        <div class="header-text">
            <div class="state-text">Estado do Rio de Janeiro</div>
            <div class="city-text">Prefeitura Municipal de Mangaratiba</div>
            <div class="dept-text">Secretaria Municipal de Ciência e Tecnologia</div>
        </div>
    </div>

    <div class="report-title">
        RELATÓRIO DETALHADO DE PROCESSO DE PAGAMENTO
    </div>

    <div class="update-date">
        Gerado em: {{ $hoje }}
    </div>

    <div class="section-title">DADOS DO PROCESSO</div>
    <table class="info-table">
        <tr>
            <th>Número do Processo</th>
            <td>{{ $processo->numero_processo }}</td>
        </tr>
        <tr>
            <th>Tipo / Categoria</th>
            <td>{{ $processo->tipo?->nome ?? 'N/A' }} / {{ $processo->categoria?->nome ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Secretaria</th>
            <td>{{ $processo->secretaria?->nome ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Empresa</th>
            <td>{{ $processo->empresa?->nome ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Validade</th>
            <td>{{ $processo->validade_processo ? $processo->validade_processo->format('d/m/Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Localização / Status</th>
            <td>{{ $processo->localizacao ?? 'N/A' }} / {{ $processo->status?->nome ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Dias Decorridos</th>
            <td>{{ $processo->dias ?? '0' }} dias</td>
        </tr>
    </table>

    <div class="section-title">OBJETO</div>
    <div class="objeto">{{ $processo->objeto ?? 'Sem objeto informado.' }}</div>

    @if($processo->notaFiscal)
    <div class="section-title">NOTA FISCAL VINCULADA</div>
    <table class="info-table">
        <tr>
            <th>Número NF</th>
            <td>{{ $processo->notaFiscal->numero_nf }}</td>
        </tr>
        <tr>
            <th>Valor</th>
            <td>R$ {{ number_format($processo->notaFiscal->valor_nf, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Data de Emissão</th>
            <td>{{ $processo->notaFiscal->data_emissao ? $processo->notaFiscal->data_emissao->format('d/m/Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Mês de Referência</th>
            <td>{{ $processo->notaFiscal->data_referencia ?? 'N/A' }}</td>
        </tr>
    </table>
    @endif

    @if($processo->statusHistoricos->isNotEmpty())
    <div class="section-title">HISTÓRICO DE STATUS</div>
    <table class="info-table">
        <thead>
            <tr>
                <th style="width: 25%">Status</th>
                <th style="width: 20%">Data</th>
                <th>Observação</th>
            </tr>
        </thead>
        <tbody>
            @foreach($processosHist = $processo->statusHistoricos->sortByDesc('created_at') as $historico)
            <tr>
                <td style="background-color: transparent; font-weight: normal;">{{ $historico->status?->nome ?? 'N/A' }}</td>
                <td>{{ $historico->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $historico->observacao ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        Relatório gerado pelo sistema - {{ config('app.name') }}
    </div>
</body>
</html>
