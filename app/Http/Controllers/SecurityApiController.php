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
     * Action to register a user.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {

        $validated = $request->validated();

        $validated['password'] = Hash::make($validated['password']);

        $user = User::query()->create($validated);

        return response()->json($user, Response::HTTP_CREATED);
    }

    /**
     * Action to log in a user. Returns an error if the credentials are invalid.
     *
     * @param LoginRequest $loginRequest
     * @return TokenResource
     * @throws ValidationException
     */
    public function login(LoginRequest $loginRequest): TokenResource
    {
        $validated = $loginRequest->validated();

        $user = User::query()->where('email', $validated["email"])->first();

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
     * Action to create an authentication token.
     * So the user can have a valid token when the current token is near to expire.
     *
     * @param Request $request
     * @return TokenResource
     */
    public function createToken(Request $request): TokenResource
    {
        $tokenName = Str::lower(Str::slug($request->user()->name . ' AuthToken'));
        $token = $request->user()->createToken($tokenName, ['*'], now()->addWeek());
        return new TokenResource($token);
    }

    /**
     * Action to log out a user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(status: Response::HTTP_NO_CONTENT);
    }
}
