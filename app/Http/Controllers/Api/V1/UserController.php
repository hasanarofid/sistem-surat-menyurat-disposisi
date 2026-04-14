<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService;

class UserController extends Controller
{
    protected $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $result = $this->service->listUsers($request->all());
        return response()->json($result);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role_id' => 'exists:roles,id',
            'signature_image' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
        ]);

        $result = $this->service->storeUser($data);
        return response()->json($result, 201);
    }

    public function show($id)
    {
        $result = $this->service->getUser($id);
        return response()->json($result);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'string',
            'email' => 'email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'role_id' => 'exists:roles,id',
            'signature_image' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
        ]);

        $result = $this->service->updateUser($id, $data);
        return response()->json($result);
    }

    public function destroy($id)
    {
        $this->service->deleteUser($id);
        return response()->json(['message' => 'User deleted successfully']);
    }
}
