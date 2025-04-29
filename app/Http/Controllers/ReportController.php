<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Consulta o serviço externo de relatórios de receita.
     * 
     * Rota protegida por autenticação (auth:sanctum, auth:api, etc.).
     * Espera receber parâmetros GET 'start' e 'end' representando datas.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function revenue(Request $request)
    {
        // Obtém parâmetros de data do request
        $start = $request->query('start');
        $end = $request->query('end');

        // Validação básica dos parâmetros
        if (!$start || !$end) {
            return response()->json([
                'message' => 'Parâmetros "start" e "end" são obrigatórios.'
            ], 422);
        }

        // URL do serviço de relatórios (com fallback padrão)
        $serviceUrl = rtrim(config('services.reports_service_url'), '/') . '/reports/revenue';

        try {
            // Realiza a requisição ao serviço Python
            $response = Http::timeout(5)->get($serviceUrl, [
                'start' => $start,
                'end' => $end,
            ]);

            // Trata resposta bem-sucedida
            if ($response->successful()) {
                return response()->json($response->json(), 200);
            }

            // Trata resposta 404 (nenhum relatório encontrado)
            if ($response->status() === 404) {
                return response()->json([
                    'message' => 'Nenhum relatório encontrado para o período informado.'
                ], 404);
            }

            // Trata qualquer outra falha genérica
            return response()->json([
                'message' => 'Erro ao consultar serviço de relatório.',
                'status' => $response->status(),
            ], 500);
        } catch (\Exception $e) {
            // Loga erro de comunicação
            Log::error('Erro ao consultar serviço de relatórios', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Erro ao comunicar com serviço de relatório.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
