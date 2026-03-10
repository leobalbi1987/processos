<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Estado do Rio de Janeiro - Relação de Processos</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #000; line-height: 1.2; }

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

        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; table-layout: fixed; }
        .info-table th, .info-table td { border: 1px solid #000; padding: 4px 2px; text-align: center; word-wrap: break-word; }
        .info-table th { background-color: #f2f2f2; font-weight: bold; font-size: 9px; }

        /* Larguras das colunas baseadas na imagem */
        .col-empresa { width: 12%; }
        .col-processo { width: 12%; }
        .col-nf { width: 8%; }
        .col-referencia { width: 12%; }
        .col-valor { width: 12%; }
        .col-localizacao { width: 12%; }
        .col-situacao { width: 22%; }
        .col-dias { width: 8%; }

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
        RELAÇÃO DE PROCESSOS - AGUARDANDO PAGAMENTO
    </div>

    <div class="update-date">
        Atualizada em: {{ now()->format('d/m/y') }}
    </div>

    <table class="info-table">
        <thead>
            <tr>
                <th class="col-empresa">Empresa</th>
                <th class="col-processo">Nº Processo</th>
                <th class="col-nf">Nota Fiscal</th>
                <th class="col-referencia">Mês de Referência</th>
                <th class="col-valor">Valor</th>
                <th class="col-localizacao">Localização</th>
                <th class="col-situacao">Status</th>
                <th class="col-dias">Dias</th>
            </tr>
        </thead>
        <tbody>
            @foreach($processos as $processo)
            <tr>
                <td>{{ $processo->empresa?->nome ?? 'N/A' }}</td>
                <td>{{ $processo->numero_processo }}</td>
                <td>{{ $processo->notaFiscal?->numero_nf ?? 'N/A' }}</td>
                <td>{{ $processo->notaFiscal?->data_referencia ?? 'N/A' }}</td>
                <td>R$ {{ number_format($processo->notaFiscal?->valor_nf ?? 0, 2, ',', '.') }}</td>
                <td>{{ $processo->localizacao ?? 'N/A' }}</td>
                <td>{{ $processo->status?->nome ?? 'N/A' }}</td>
                <td>{{ $processo->dias ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Relatório gerado em {{ $hoje }}
    </div>
</body>
</html>
