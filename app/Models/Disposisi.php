<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Disposisi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'disposisi';

    protected $fillable = [
        'surat_masuk_id',
        'pengirim_id',
        'penerima_id',
        'instruksi',
        'catatan',
        'status',
    ];

    public function suratMasuk()
    {
        return $this->belongsTo(SuratMasuk::class, 'surat_masuk_id');
    }

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }

    public function penerima()
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }
}
