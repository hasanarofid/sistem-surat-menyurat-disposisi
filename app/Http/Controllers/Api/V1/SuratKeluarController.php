<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SuratKeluarService;

class SuratKeluarController extends Controller
{
    protected $service;

    public function __construct(SuratKeluarService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $result = $this->service->listSurat($request->all());
        return response()->json($result);
    }

    public function generateNumber()
    {
        $result = $this->service->generateNumber();
        return response()->json($result);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nomor_surat' => 'required|string|unique:surat_keluar,nomor_surat',
            'tanggal_surat' => 'required|date',
            'tujuan_surat' => 'required|string',
            'perihal' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'is_auto_generated' => 'nullable'
        ]);

        // Normalize boolean from string "1"/"0" or "true"/"false"
        if (isset($data['is_auto_generated'])) {
            $data['is_auto_generated'] = filter_var($data['is_auto_generated'], FILTER_VALIDATE_BOOLEAN);
        }

        $result = $this->service->storeSurat($data);
        return response()->json($result, 201);
    }

    public function sign($id)
    {
        $result = $this->service->signSurat($id, Auth::id());
        return response()->json($result);
    }

    public function show($id)
    {
        $result = $this->service->getSurat($id);
        return response()->json($result);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nomor_surat' => 'string|unique:surat_keluar,nomor_surat,' . $id,
            'tanggal_surat' => 'date',
            'tujuan_surat' => 'string',
            'perihal' => 'string',
            'file' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'status' => 'string|in:draft,sent',
        ]);

        $result = $this->service->updateSurat($id, $data);
        return response()->json($result);
    }

    public function destroy($id)
    {
        $this->service->deleteSurat($id);
        return response()->json(['message' => 'Deleted successfully']);
    }
}
