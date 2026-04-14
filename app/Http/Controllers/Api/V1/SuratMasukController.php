<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SuratMasukService;

class SuratMasukController extends Controller
{
    protected $service;

    public function __construct(SuratMasukService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $result = $this->service->listSurat($request->all());
        return response()->json($result);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nomor_surat' => 'required|string|unique:surat_masuk,nomor_surat',
            'tanggal_surat' => 'required|date',
            'tanggal_terima' => 'required|date',
            'asal_surat' => 'required|string',
            'perihal' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $result = $this->service->storeSurat($data + ['file' => $request->file('file')]);
        return response()->json($result, 201);
    }

    public function show($id)
    {
        $result = $this->service->getSurat($id);
        return response()->json($result);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nomor_surat' => 'string|unique:surat_masuk,nomor_surat,' . $id,
            'tanggal_surat' => 'date',
            'tanggal_terima' => 'date',
            'asal_surat' => 'string',
            'perihal' => 'string',
            'file' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'status' => 'string|in:pending,processed,archived',
        ]);

        $result = $this->service->updateSurat($id, $data + ['file' => $request->file('file')]);
        return response()->json($result);
    }

    public function destroy($id)
    {
        $this->service->deleteSurat($id);
        return response()->json(['message' => 'Deleted successfully']);
    }
}
