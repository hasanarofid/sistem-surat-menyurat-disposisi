<?php

namespace App\Services;

use App\Repositories\SuratMasukRepository;
use Illuminate\Support\Facades\Storage;

class SuratMasukService
{
    protected $repository;
    protected $numberingService;

    public function __construct(SuratMasukRepository $repository, NumberingService $numberingService)
    {
        $this->repository = $repository;
        $this->numberingService = $numberingService;
    }

    public function generateNumber()
    {
        return $this->numberingService->generateNextNumber('masuk');
    }

    public function listSurat(array $filters)
    {
        return $this->repository->getAll($filters);
    }

    public function storeSurat(array $data)
    {
        if (isset($data['file'])) {
            $data['file_path'] = $data['file']->store('surat_masuk', 'public');
            unset($data['file']);
        }

        if (isset($data['is_auto_generated']) && $data['is_auto_generated']) {
            $this->numberingService->incrementCounter('masuk');
        }

        return $this->repository->create($data);
    }

    public function getSurat($id)
    {
        return $this->repository->findById($id)->load('disposisi.pengirim', 'disposisi.penerima');
    }

    public function updateSurat($id, array $data)
    {
        $surat = $this->repository->findById($id);

        if (isset($data['file'])) {
            if ($surat->file_path) {
                // Potential cleanup here
            }
            $data['file_path'] = $data['file']->store('surat_masuk', 'public');
            unset($data['file']);
        }

        return $this->repository->update($id, $data);
    }

    public function deleteSurat($id)
    {
        $surat = $this->repository->findById($id);
        if ($surat->file_path) {
            Storage::delete($surat->file_path);
        }
        return $this->repository->delete($id);
    }
}
