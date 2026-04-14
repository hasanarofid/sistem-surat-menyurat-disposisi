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
        if (isset($data['signature_image']) && $data['signature_image'] instanceof \Illuminate\Http\UploadedFile) {
            $data['signature_image'] = Storage::url($data['signature_image']->store('signatures', 'public'));
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
        if (isset($data['signature_image']) && $data['signature_image'] instanceof \Illuminate\Http\UploadedFile) {
            $data['signature_image'] = Storage::url($data['signature_image']->store('signatures', 'public'));
        }
        return $this->repository->update($id, $data);
    }

    public function deleteUser($id)
    {
        return $this->repository->delete($id);
    }
}
