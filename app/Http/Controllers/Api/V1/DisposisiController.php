<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DisposisiService;

class DisposisiController extends Controller
{
    protected $service;

    public function __construct(DisposisiService $service)
    {
        $this->service = $service;
    }

    public function all(Request $request)
    {
        $result = $this->service->listAll($request->all());
        return response()->json($result);
    }

    public function index($surat_id)
    {
        $result = $this->service->getDisposisi($surat_id);
        return response()->json($result);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'surat_masuk_id' => 'required|exists:surat_masuk,id',
            'penerima_id' => 'required|exists:users,id',
            'instruksi' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        $data['pengirim_id'] = $request->user()->id;

        $result = $this->service->createDisposisi($data);
        return response()->json($result, 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:pending,completed']);
        $result = $this->service->updateStatus($id, $request->status);
        return response()->json($result);
    }
}
