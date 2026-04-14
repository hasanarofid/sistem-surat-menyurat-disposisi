<?php

namespace App\Services;

use App\Repositories\DisposisiRepository;
use App\Models\SuratMasuk;

class DisposisiService
{
    protected $repository;

    public function __construct(DisposisiRepository $repository)
    {
        $this->repository = $repository;
    }

    public function listAll(array $filters)
    {
        return $this->repository->getAll($filters);
    }

    public function createDisposisi(array $data)
    {
        // Update surat status to processed
        SuratMasuk::where('id', $data['surat_masuk_id'])->update(['status' => 'processed']);
        
        return $this->repository->create($data);
    }

    public function getDisposisi($suratId)
    {
        return $this->repository->getBySuratId($suratId);
    }

    public function updateStatus($id, $status)
    {
        return $this->repository->update($id, ['status' => $status]);
    }
}
