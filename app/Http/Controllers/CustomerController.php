<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Class CustomerController
 *
 * Controlador para gerenciar operações de CRUD para clientes.
 * Este controlador lida com a criação, leitura, atualização e exclusão de clientes.
 *
 * @package App\Http\Controllers
 */
class CustomerController extends Controller
{
    /**
     * Lista os clientes de forma paginada.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $customers = Customer::paginate(10);
        return response()->json($customers);
    }

    /**
     * Exibe os detalhes de um cliente específico.
     *
     * @param  Customer  $customer  O cliente cujos detalhes devem ser retornados.
     * @return JsonResponse
     */
    public function show(Customer $customer): JsonResponse
    {
        try {
            return response()->json($customer);
        } catch (\Throwable $e) {
            Log::error('Erro ao buscar cliente', ['id' => $customer->id, 'exception' => $e]);
            return response()->json(['error' => 'Cliente não encontrado.'], 404);
        }
    }

    /**
     * Cria um novo cliente.
     *
     * @param  CustomerStoreRequest  $request  O objeto de requisição com os dados do novo cliente.
     * @return JsonResponse
     */
    public function store(CustomerStoreRequest $request): JsonResponse
    {
        try {
            $customer = Customer::create($request->validated());
            return response()->json($customer, 201);
        } catch (\Throwable $e) {
            Log::error('Erro ao criar cliente', ['data' => $request->validated(), 'exception' => $e]);
            return response()->json(['error' => 'Erro ao criar cliente.'], 500); 
        }
    }

    /**
     * Atualiza os dados de um cliente existente.
     *
     * @param  CustomerUpdateRequest  $request  O objeto de requisição com os dados a serem atualizados.
     * @param  Customer  $customer  O cliente a ser atualizado.
     * @return JsonResponse
     */
    public function update(CustomerUpdateRequest $request, Customer $customer): JsonResponse
    {
        try {
            $customer->update($request->validated());
            return response()->json($customer);
        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar cliente', ['id' => $customer->id, 'data' => $request->validated(), 'exception' => $e]);
            return response()->json(['error' => 'Erro ao atualizar cliente.'], 500);
        }
    }

    /**
     * Deleta um cliente existente.
     *
     * @param  Customer  $customer  O cliente a ser deletado.
     * @return JsonResponse
     */
    public function destroy(Customer $customer): JsonResponse
    {
        try {
            $customer->delete();
            return response()->json(null, 204);
        } catch (\Throwable $e) {
            Log::error('Erro ao deletar cliente', ['id' => $customer->id, 'exception' => $e]);
            return response()->json(['error' => 'Erro ao deletar cliente.'], 500); 
        }
    }
}
