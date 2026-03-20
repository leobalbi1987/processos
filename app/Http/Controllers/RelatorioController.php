<?php

namespace App\Http\Controllers;

use App\Models\Processo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    public function processo(Processo $processo)
    {
        // Carrega relacionamentos necessários
        $processo->load(['empresa', 'secretaria', 'tipo', 'categoria', 'notaFiscal', 'statusHistoricos.status']);

        $logoPath = public_path('img/1200px-Brasao_mangaratiba.jpg');
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoBase64 = 'data:image/jpeg;base64,' . $logoData;

        $logoRightPath = public_path('img/logo.png');
        $logoRightData = base64_encode(file_get_contents($logoRightPath));
        $logoRightBase64 = 'data:image/png;base64,' . $logoRightData;

        // Dados para o PDF
        $data = [
            'processo' => $processo,
            'hoje' => now()->format('d/m/Y H:i'),
            'logo' => $logoBase64,
            'logo_right' => $logoRightBase64,
        ];

        // Gera o PDF a partir da view
        $pdf = Pdf::loadView('reports.processo', $data)->setPaper('a4', 'landscape');

        // Retorna o PDF para o navegador (stream para abrir em nova aba)
        return $pdf->stream("relatorio_processo_{$processo->numero_processo}.pdf");
    }

    public function processosEmLote(Request $request)
    {
        $processoIds = $request->input('processos', []);
        if (empty($processoIds)) {
            abort(400, 'Nenhum processo selecionado.');
        }

        $processos = Processo::with(['empresa', 'notaFiscal'])->whereIn('id', $processoIds)->get();

        $logoPath = public_path('img/1200px-Brasão_mangaratiba.jpg');
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoBase64 = 'data:image/jpeg;base64,' . $logoData;

        $logoRightPath = public_path('img/logo.png');
        $logoRightData = base64_encode(file_get_contents($logoRightPath));
        $logoRightBase64 = 'data:image/png;base64,' . $logoRightData;

        $data = [
            'processos' => $processos,
            'hoje' => now()->format('d/m/Y H:i'),
            'logo' => $logoBase64,
            'logo_right' => $logoRightBase64,
        ];

        $pdf = Pdf::loadView('reports.processos_lote', $data)->setPaper('a4', 'landscape');

        return $pdf->stream('relatorio_processos_em_lote.pdf');
    }
}
