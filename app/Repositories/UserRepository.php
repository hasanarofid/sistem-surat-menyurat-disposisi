<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function getAll(array $filters = [])
    {
        return User::with('roles')->latest()->paginate($filters['per_page'] ?? 10);
    }

    public function findById($id)
    {
        return User::with('roles.permissions')->findOrFail($id);
    }

    public function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        if (isset($data['role_id'])) {
            $user->roles()->attach($data['role_id']);
        }

        return $user;
    }

    public function update($id, array $data)
    {
        $user = $this->findById($id);
        
        $updateData = [
            'name' => $data['name'] ?? $user->name,
            'email' => $data['email'] ?? $user->email,
        ];

        if (isset($data['password'])) {
            $updateData['password'] = bcrypt($data['password']);
        }

        $user->update($updateData);

        if (isset($data['role_id'])) {
            $user->roles()->sync([$data['role_id']]);
        }

        return $user;
    }

    public function delete($id)
    {
        $user = $this->findById($id);
        return $user->delete();
    }
}
