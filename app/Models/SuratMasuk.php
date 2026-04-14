<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratMasuk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surat_masuk';

    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'tanggal_terima',
        'asal_surat',
        'perihal',
        'file_path',
        'status',
    ];

    public function disposisi()
    {
        return $this->hasMany(Disposisi::class, 'surat_masuk_id');
    }
}
