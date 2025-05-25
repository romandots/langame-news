<?php

namespace App\Http\Controllers;

use App\Http\Requests\FetchUsersRequest;
use App\Services\Users\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index');
    }

    public function fetch(UserService $service, FetchUsersRequest $request): JsonResponse
    {
        $fetchUsers = $request->getDto();
        $usersResponse = $service->fetch($fetchUsers);
        return response()->json($usersResponse);
    }
}
