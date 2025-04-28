<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Class AuthController
 *
 * Controlador responsável por gerenciar autenticação de usuários via JWT.
 *
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * Registra um novo usuário e gera um token JWT.
     *
     * @param  RegisterRequest  $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'user' => $user,
                'token' => JWTAuth::fromUser($user),
            ], 201);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Erro ao registrar usuário.'], 500);
        }
    }

    /**
     * Autentica o usuário e gera um token JWT.
     *
     * @param  LoginRequest  $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Credenciais inválidas.'], 401);
            }

            return response()->json(['token' => $token]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Erro ao gerar token.'], 500);
        }
    }

    /**
     * Realiza logout do usuário, invalidando o token JWT.
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            $token = JWTAuth::getToken();

            if (!$token) {
                return response()->json(['error' => 'Token não fornecido.'], 400);
            }

            JWTAuth::invalidate($token);

            return response()->json(null, 204);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Erro ao realizar logout.'], 500);
        }
    }
}
