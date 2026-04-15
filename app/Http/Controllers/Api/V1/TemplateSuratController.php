<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TemplateSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TemplateSuratController extends Controller
{
    public function index(Request $request)
    {
        $query = TemplateSurat::query()->with('creator:id,name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_template', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('kode_template', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jenis_surat')) {
            $query->whereIn('jenis_surat', [$request->jenis_surat, 'umum']);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = $request->get('per_page', 10);
        $result = $query->latest()->paginate($perPage);

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_template'       => 'required|string|max:255',
            'kode_template'       => 'nullable|string|max:50|unique:template_surat,kode_template',
            'deskripsi'           => 'nullable|string',
            'kop_nama_instansi'   => 'nullable|string|max:255',
            'kop_alamat'          => 'nullable|string',
            'kop_telp'            => 'nullable|string|max:50',
            'kop_email'           => 'nullable|email|max:255',
            'kop_website'         => 'nullable|string|max:255',
            'kop_logo'            => 'nullable|image|mimes:png,jpg,jpeg,svg|max:1024',
            'body_template'       => 'nullable|string',
            'footer_kota'         => 'nullable|string|max:100',
            'footer_jabatan_ttd'  => 'nullable|string|max:255',
            'footer_nama_ttd'     => 'nullable|string|max:255',
            'footer_nip_ttd'      => 'nullable|string|max:100',
            'jenis_surat'         => 'nullable|in:masuk,keluar,umum',
            'is_active'           => 'nullable|boolean',
        ]);

        if ($request->hasFile('kop_logo')) {
            $data['kop_logo_path'] = $request->file('kop_logo')->store('template-logo', 'public');
        }

        unset($data['kop_logo']);
        $data['created_by'] = Auth::id();

        $template = TemplateSurat::create($data);

        return response()->json($template->load('creator:id,name'), 201);
    }

    public function show($id)
    {
        $template = TemplateSurat::with('creator:id,name')->findOrFail($id);
        return response()->json($template);
    }

    public function update(Request $request, $id)
    {
        $template = TemplateSurat::findOrFail($id);

        $data = $request->validate([
            'nama_template'       => 'sometimes|string|max:255',
            'kode_template'       => 'nullable|string|max:50|unique:template_surat,kode_template,' . $id,
            'deskripsi'           => 'nullable|string',
            'kop_nama_instansi'   => 'nullable|string|max:255',
            'kop_alamat'          => 'nullable|string',
            'kop_telp'            => 'nullable|string|max:50',
            'kop_email'           => 'nullable|email|max:255',
            'kop_website'         => 'nullable|string|max:255',
            'kop_logo'            => 'nullable|image|mimes:png,jpg,jpeg,svg|max:1024',
            'body_template'       => 'nullable|string',
            'footer_kota'         => 'nullable|string|max:100',
            'footer_jabatan_ttd'  => 'nullable|string|max:255',
            'footer_nama_ttd'     => 'nullable|string|max:255',
            'footer_nip_ttd'      => 'nullable|string|max:100',
            'jenis_surat'         => 'nullable|in:masuk,keluar,umum',
            'is_active'           => 'nullable|boolean',
        ]);

        if ($request->hasFile('kop_logo')) {
            // Hapus logo lama jika ada
            if ($template->kop_logo_path) {
                Storage::disk('public')->delete($template->kop_logo_path);
            }
            $data['kop_logo_path'] = $request->file('kop_logo')->store('template-logo', 'public');
        }

        unset($data['kop_logo']);

        $template->update($data);

        return response()->json($template->fresh()->load('creator:id,name'));
    }

    public function destroy($id)
    {
        $template = TemplateSurat::findOrFail($id);

        if ($template->kop_logo_path) {
            Storage::disk('public')->delete($template->kop_logo_path);
        }

        $template->delete();

        return response()->json(['message' => 'Template berhasil dihapus']);
    }

    /**
     * Render preview HTML dari template dengan data contoh
     */
    public function preview($id)
    {
        $template = TemplateSurat::findOrFail($id);

        $sampleData = [
            '{{nomor_surat}}'    => 'SK-BLU/2026/001',
            '{{tanggal_surat}}'  => now()->translatedFormat('d F Y'),
            '{{tujuan_surat}}'   => 'Kepala Dinas Kesehatan',
            '{{perihal}}'        => 'Undangan Rapat Koordinasi',
            '{{nama_instansi}}'  => $template->kop_nama_instansi ?? 'Nama Instansi',
            '{{ttd_nama}}'       => $template->footer_nama_ttd ?? 'Nama Penandatangan',
            '{{ttd_jabatan}}'    => $template->footer_jabatan_ttd ?? 'Jabatan',
            '{{ttd_nip}}'        => $template->footer_nip_ttd ?? 'NIP. -',
            '{{kota_tanggal}}'   => ($template->footer_kota ?? 'Jakarta') . ', ' . now()->translatedFormat('d F Y'),
        ];

        $body = str_replace(array_keys($sampleData), array_values($sampleData), $template->body_template ?? '');

        return response()->json([
            'template' => $template,
            'rendered_body' => $body,
            'placeholders' => array_keys($sampleData),
        ]);
    }
}
