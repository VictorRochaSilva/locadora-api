<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\ReportController;

/**
 * Rotas de autenticação (registro e login).
 *
 * Essas rotas são responsáveis por registrar um novo usuário e permitir o login 
 * do usuário para acessar os recursos da API com um token JWT.
 */
Route::post('/register', [AuthController::class, 'register']);  // Rota para registrar um novo usuário
Route::post('/login', [AuthController::class, 'login']);        // Rota para login de usuário

/**
 * Rotas protegidas por autenticação via token JWT.
 * 
 * Estas rotas estão acessíveis apenas para usuários autenticados, após o login bem-sucedido.
 */
Route::middleware('auth:api')->group(function () {

    /**
     * Rota para realizar o logout.
     * 
     * A rota invalida o token JWT do usuário, efetivamente desconectando-o.
     */
    Route::post('/logout', [AuthController::class, 'logout']);

    /**
     * Rota de busca de veículos.
     * 
     * Permite realizar a pesquisa de veículos através de um termo de pesquisa (query).
     */
    Route::get('/vehicles/search', [VehicleController::class, 'search']);   // Busca de veículos com elasticsearch

    /**
     * Rotas para gerenciamento de veículos, clientes e aluguéis.
     * 
     * Essas rotas seguem o padrão de um CRUD (Create, Read, Update, Delete).
     */
    Route::apiResource('vehicles', VehicleController::class);   // CRUD de veículos
    Route::apiResource('customers', CustomerController::class); // CRUD de clientes
    Route::apiResource('rentals', RentalController::class);     // CRUD de aluguéis

    /**
     * Rotas para iniciar e finalizar um aluguel de veículo.
     * 
     * Essas rotas são responsáveis por iniciar e finalizar o aluguel de um veículo.
     */
    Route::post('/rentals/{rental}/start', [RentalController::class, 'start']);  // Inicia o aluguel
    Route::post('/rentals/{rental}/end', [RentalController::class, 'end']);      // Finaliza o aluguel


    /**
     * Rota para gerar relatórios de faturamento.
     * 
     */
    Route::get('/reports/revenue', [ReportController::class, 'revenue']);
});
