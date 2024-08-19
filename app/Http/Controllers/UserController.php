<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController
{
    /**
     * Action to get the authenticated user.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return (new UserResource(auth()->user()))->response()->setStatusCode(200);
    }

    /**
     * Action to get all users.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = User::query()->paginate();
        return (new UserCollection($users))->response()->setStatusCode(200);
    }
}
