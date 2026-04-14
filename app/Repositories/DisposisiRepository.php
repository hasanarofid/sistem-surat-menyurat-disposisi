<?php

namespace App\Repositories;

use App\Models\Disposisi;

class DisposisiRepository
{
    public function getAll(array $filters = [])
    {
        $query = Disposisi::query()->with('suratMasuk', 'pengirim', 'penerima');

        if (isset($filters['search'])) {
            $query->whereHas('suratMasuk', function($q) use ($filters) {
                $q->where('perihal', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('nomor_surat', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->latest()->paginate($filters['per_page'] ?? 10);
    }

    public function getBySuratId($suratId)
    {
        return Disposisi::where('surat_masuk_id', $suratId)
                        ->with('pengirim', 'penerima')
                        ->latest()
                        ->get();
    }

    public function create(array $data)
    {
        return Disposisi::create($data);
    }

    public function update($id, array $data)
    {
        $disposisi = Disposisi::findOrFail($id);
        $disposisi->update($data);
        return $disposisi;
    }

    public function delete($id)
    {
        $disposisi = Disposisi::findOrFail($id);
        return $disposisi->delete();
    }
}
