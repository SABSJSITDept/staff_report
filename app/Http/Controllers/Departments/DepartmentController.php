<?php

namespace App\Http\Controllers\Departments;

use App\Http\Controllers\Controller;
use App\Models\Office\DepartmentModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(): JsonResponse
    {
        $departments = DepartmentModel::with('hod')->latest()->get()->map(function($d) {
            return [
                'id' => $d->id,
                'name' => $d->name,
                'status' => $d->status,
                'hod_id' => $d->hod_id,
                'hod_name' => $d->hod ? $d->hod->name : null,
                'created_at' => $d->created_at,
                'updated_at' => $d->updated_at
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $departments,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:Active,Inactive'],
            'hod_id' => ['nullable', 'exists:staff_details,id']
        ]);

        $validated['name'] = strtoupper($validated['name']);

        $department = DepartmentModel::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Department successfully create ho gaya!',
            'data'    => $department,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $department = DepartmentModel::with('hod')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $department,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $department = DepartmentModel::findOrFail($id);

        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:Active,Inactive'],
            'hod_id' => ['nullable', 'exists:staff_details,id']
        ]);

        $validated['name'] = strtoupper($validated['name']);

        $department->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Department successfully update ho gaya!',
            'data'    => $department,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $department = DepartmentModel::findOrFail($id);
        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'Department successfully delete ho gaya!',
        ]);
    }
}
