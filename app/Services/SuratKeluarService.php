<?php

namespace App\Services;

use App\Repositories\SuratKeluarRepository;
use Illuminate\Support\Facades\Storage;

class SuratKeluarService
{
    protected $repository;
    protected $numberingService;

    public function __construct(SuratKeluarRepository $repository, NumberingService $numberingService)
    {
        $this->repository = $repository;
        $this->numberingService = $numberingService;
    }

    public function listSurat(array $filters)
    {
        return $this->repository->getAll($filters);
    }

    public function generateNumber()
    {
        return $this->numberingService->generateNextNumber();
    }

    public function storeSurat(array $data)
    {
        if (isset($data['file'])) {
            $data['file_path'] = $data['file']->store('surat_keluar');
            unset($data['file']);
        }

        // Increment counter if this number was generated automatically
        // For simplicity, we assume if it matches the generateNumber pattern or specific flag
        if (isset($data['is_auto_generated']) && $data['is_auto_generated']) {
            $this->numberingService->incrementCounter();
        }

        return $this->repository->create($data);
    }

    public function signSurat($id, $userId)
    {
        $user = \App\Models\User::find($userId);
        return $this->repository->update($id, [
            'is_signed' => true,
            'signed_at' => now(),
            'signature_path' => $user->signature_image, // Use default signature image
            'status' => 'sent' // Automatically set to sent when signed
        ]);
    }

    public function getSurat($id)
    {
        return $this->repository->findById($id);
    }

    public function updateSurat($id, array $data)
    {
        $surat = $this->repository->findById($id);

        if (isset($data['file'])) {
            if ($surat->file_path) {
                Storage::delete($surat->file_path);
            }
            $data['file_path'] = $data['file']->store('surat_keluar');
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
