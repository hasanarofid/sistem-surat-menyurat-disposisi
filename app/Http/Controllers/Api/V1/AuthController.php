<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $result = $this->authService->login(
            $request->only('email', 'password'),
            $request->device_name
        );

        return response()->json($result);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load('roles.permissions'));
    }
}
