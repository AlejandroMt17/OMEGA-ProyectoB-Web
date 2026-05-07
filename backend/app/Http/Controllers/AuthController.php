<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $auth
    ) {}

    public function login(Request $request): JsonResponse
    {
        $resultado = $this->auth->login($request->all());
        return response()->json(['data' => $resultado], 200);
    }

    public function register(Request $request): JsonResponse
    {
        $resultado = $this->auth->register($request->all());
        return response()->json(['data' => $resultado], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->auth->logout($request->user());
        return response()->json(['message' => 'Sesion cerrada correctamente.']);
    }

    public function me(Request $request): JsonResponse
    {
        $resultado = $this->auth->me($request->user());
        return response()->json(['data' => $resultado]);
    }
}