<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleStoreRequest;
use App\Http\Requests\VehicleUpdateRequest;
use App\Models\Vehicle;
use App\Services\ElasticVehicleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

/**
 * Class VehicleController
 *
 * Gerencia operações relacionadas a veículos.
 *
 * @package App\Http\Controllers
 */
class VehicleController extends Controller
{
    /**
     * Lista veículos paginados.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $vehicles = Vehicle::paginate(10);
        return response()->json($vehicles);
    }

    /**
     * Exibe detalhes de um veículo específico.
     *
     * @param Vehicle $vehicle
     * @return JsonResponse
     */
    public function show(Vehicle $vehicle): JsonResponse
    {
        return response()->json($vehicle);
    }

    /**
     * Cria um novo veículo.
     *
     * @param VehicleStoreRequest $request
     * @return JsonResponse
     */
    public function store(VehicleStoreRequest $request): JsonResponse
    {
        try {
            $vehicle = Vehicle::create($request->validated());
            return response()->json($vehicle, 201);
        } catch (\Throwable $e) {
            Log::error('Erro ao criar veículo', ['data' => $request->validated(), 'exception' => $e]);
            return response()->json(['error' => 'Erro ao criar veículo.'], 500);
        }
    }

    /**
     * Atualiza um veículo existente.
     *
     * @param VehicleUpdateRequest $request
     * @param Vehicle $vehicle
     * @return JsonResponse
     */
    public function update(VehicleUpdateRequest $request, Vehicle $vehicle): JsonResponse
    {
        try {
            $vehicle->update($request->validated());
            return response()->json($vehicle);
        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar veículo', ['id' => $vehicle->id, 'exception' => $e]);
            return response()->json(['error' => 'Erro ao atualizar veículo.'], 500);
        }
    }

    /**
     * Remove um veículo existente.
     *
     * @param Vehicle $vehicle
     * @return JsonResponse
     */
    public function destroy(Vehicle $vehicle): JsonResponse
    {
        try {
            $vehicle->delete();
            return response()->json(null, 204);
        } catch (\Throwable $e) {
            Log::error('Erro ao deletar veículo', ['id' => $vehicle->id, 'exception' => $e]);
            return response()->json(['error' => 'Erro ao deletar veículo.'], 500);
        }
    }

    /**
     * Busca veículos no Elasticsearch.
     *
     * @param Request $request
     * @param ElasticVehicleService $elasticService
     * @return JsonResponse
     */
    public function search(Request $request, ElasticVehicleService $elasticService): JsonResponse
    {
        try {
            $query = $request->input('q');
            if (empty($query)) {
                return response()->json(['error' => 'Parâmetro de busca "q" é obrigatório.'], 400);
            }

            $vehicles = $elasticService->searchVehicle($query);
            return response()->json($vehicles);
        } catch (\Throwable $e) {
            Log::error('Erro ao buscar veículos', ['exception' => $e]);
            return response()->json(['error' => 'Erro na busca de veículos.'], 500);
        }
    }
}
