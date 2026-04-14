<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthRepository
{
    public function findByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function createToken(User $user, string $deviceName)
    {
        return $user->createToken($deviceName)->plainTextToken;
    }

    public function revokeTokens(User $user)
    {
        return $user->tokens()->delete();
    }
}
