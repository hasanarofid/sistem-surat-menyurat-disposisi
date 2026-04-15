<?php

namespace App\Repositories;

use App\Models\SuratMasuk;

class SuratMasukRepository
{
    public function getAll(array $filters = [])
    {
        $query = SuratMasuk::query()->with('template:id,nama_template,kode_template,jenis_surat,deskripsi,kop_nama_instansi');

        if (isset($filters['search'])) {
            $query->where('perihal', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('nomor_surat', 'like', '%' . $filters['search'] . '%');
        }

        return $query->latest()->paginate($filters['per_page'] ?? 10);
    }

    public function findById($id)
    {
        return SuratMasuk::with('template:id,nama_template')->findOrFail($id);
    }

    public function create(array $data)
    {
        return SuratMasuk::create($data);
    }

    public function update($id, array $data)
    {
        $surat = $this->findById($id);
        $surat->update($data);
        return $surat;
    }

    public function delete($id)
    {
        $surat = $this->findById($id);
        return $surat->delete();
    }
}
