<?php

namespace App\Services;

use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function login(array $credentials, string $deviceName)
    {
        $user = $this->authRepository->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $this->authRepository->createToken($user, $deviceName);

        return [
            'user' => $user->load('roles.permissions'),
            'token' => $token,
        ];
    }

    public function logout($user)
    {
        return $this->authRepository->revokeTokens($user);
    }
}
