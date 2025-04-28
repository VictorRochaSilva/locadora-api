<?php

namespace App\Http\Controllers;

use App\Http\Requests\RentalStoreRequest;
use App\Models\Rental;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Class RentalController
 *
 * Gerencia operações de locação de veículos.
 *
 * @package App\Http\Controllers
 */
class RentalController extends Controller
{
    /**
     * Lista locações paginadas.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $rentals = Rental::with(['vehicle', 'customer'])->paginate(10);
        return response()->json(['success' => true, 'data' => $rentals]);
    }

    /**
     * Exibe detalhes de uma locação específica.
     *
     * @param  Rental  $rental
     * @return JsonResponse
     */
    public function show(Rental $rental): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $rental]);
    }

    /**
     * Cria uma nova locação.
     *
     * @param  RentalStoreRequest  $request
     * @return JsonResponse
     */
    public function store(RentalStoreRequest $request): JsonResponse
    {
        try {
            $rental = Rental::create($request->validated());
            return response()->json(['success' => true, 'data' => $rental], 201);
        } catch (\Throwable $e) {
            Log::error('Erro ao criar locação', ['data' => $request->validated(), 'exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Erro ao criar locação.'], 500);
        }
    }

    /**
     * Inicia uma locação.
     *
     * @param  Rental  $rental
     * @return JsonResponse
     */
    public function start(Rental $rental): JsonResponse
    {
        if ($rental->start_date) {
            return response()->json(['success' => false, 'message' => 'Locação já iniciada.'], 400);
        }

        $rental->start_date = now();
        $rental->save();

        return response()->json(['success' => true, 'data' => $rental]);
    }

    /**
     * Finaliza uma locação.
     *
     * @param  Rental  $rental
     * @return JsonResponse
     */
    public function end(Rental $rental): JsonResponse
    {
        if (!$rental->start_date) {
            return response()->json(['success' => false, 'message' => 'Locação ainda não iniciada.'], 400);
        }

        if ($rental->end_date) {
            return response()->json(['success' => false, 'message' => 'Locação já finalizada.'], 400);
        }

        $rental->end_date = now();
        $days = Carbon::parse($rental->start_date)->diffInDays($rental->end_date) ?: 1;
        $rental->total_amount = $days * $rental->vehicle->daily_rate;
        $rental->save();

        return response()->json(['success' => true, 'data' => $rental]);
    }
}
