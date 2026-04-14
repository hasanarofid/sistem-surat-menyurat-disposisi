<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Storage;

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
        if (isset($data['file'])) {
            $data['signature_image'] = Storage::url($data['file']->store('signatures', 'public'));
            unset($data['file']);
        }
        return $this->repository->create($data);
    }

    public function getUser($id)
    {
        return $this->repository->findById($id);
    }

    public function updateUser($id, array $data)
    {
        $user = $this->repository->findById($id);
        if (isset($data['file'])) {
            if ($user->signature_image) {
                // Potential cleanup logic here if needed
            }
            $data['signature_image'] = Storage::url($data['file']->store('signatures', 'public'));
            unset($data['file']);
        }
        return $this->repository->update($id, $data);
    }

    public function deleteUser($id)
    {
        return $this->repository->delete($id);
    }
}
