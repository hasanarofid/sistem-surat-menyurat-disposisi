<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratKeluar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surat_keluar';

    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'tujuan_surat',
        'perihal',
        'file_path',
        'status',
        'template_id',
    ];

    public function template()
    {
        return $this->belongsTo(\App\Models\TemplateSurat::class, 'template_id');
    }
}
