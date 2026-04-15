<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateSurat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'template_surat';

    protected $fillable = [
        'nama_template',
        'kode_template',
        'deskripsi',
        'kop_nama_instansi',
        'kop_alamat',
        'kop_telp',
        'kop_email',
        'kop_website',
        'kop_logo_path',
        'body_template',
        'footer_kota',
        'footer_jabatan_ttd',
        'footer_nama_ttd',
        'footer_nip_ttd',
        'jenis_surat',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
