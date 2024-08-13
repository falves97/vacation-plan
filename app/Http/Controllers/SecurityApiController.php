<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\TokenResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class SecurityApiController
{
    /**
     * Método para registrar un usuario.
     */
    public function register(RegisterRequest $request): JsonResponse
    {

        $validated = $request->validated();

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json($user, Response::HTTP_CREATED);
    }

    /**
     * Método para loguear un usuario. Retorna um erro se as credenciais são inválidas.
     * @throws ValidationException
     */
    public function login(LoginRequest $loginRequest): TokenResource
    {
        $validated = $loginRequest->validated();

        $user = User::where('email', $validated["email"])->first();

        if (!$user || !Hash::check($validated["password"], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $tokenName = Str::lower(Str::slug($user->name . ' AuthToken'));
        $token = $user->createToken($tokenName);
        return new TokenResource($token);
    }

    /**
     * Método para criar um token de autenticação. Para que o usuário possa acessar ter um token
     * válido quando o token atual estiver próximo de expirar.
     */
    public function createToken(Request $request): TokenResource
    {
        $tokenName = Str::lower(Str::slug($request->user()->name . ' AuthToken'));
        $token = $request->user()->createToken($tokenName, ['*'], now()->addWeek());
        return new TokenResource($token);
    }

    /**
     * Método para deslogar um usuário.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(status: Response::HTTP_NO_CONTENT);
    }
}
