<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function listUsers(array $filters)
    {
        return $this->repository->getAll($filters);
    }

    public function storeUser(array $data)
    {
        return $this->repository->create($data);
    }

    public function getUser($id)
    {
        return $this->repository->findById($id);
    }

    public function updateUser($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function deleteUser($id)
    {
        return $this->repository->delete($id);
    }
}
