<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PstController extends Controller
{
    public function index()
    {
        $psts = User::where('role', 'pst')->get();
        return response()->json([
            'success' => true,
            'data' => $psts
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'pst',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'PST user created successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating PST user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $pst = User::where('role', 'pst')->find($id);

        if (!$pst) {
            return response()->json([
                'success' => false,
                'message' => 'PST user not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pst
        ]);
    }

    public function update(Request $request, $id)
    {
        $pst = User::where('role', 'pst')->find($id);

        if (!$pst) {
            return response()->json([
                'success' => false,
                'message' => 'PST user not found'
            ], 404);
        }
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
        ]);

        DB::beginTransaction();
        try {
            if (isset($validated['name'])) $pst->name = $validated['name'];
            if (isset($validated['email'])) $pst->email = $validated['email'];
            if (!empty($validated['password'])) {
                $pst->password = Hash::make($validated['password']);
            }
            $pst->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'PST user updated successfully',
                'data' => $pst
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating PST user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $pst = User::where('role', 'pst')->find($id);

        if (!$pst) {
            return response()->json([
                'success' => false,
                'message' => 'PST user not found'
            ], 404);
        }

        try {
            $pst->delete();
            return response()->json([
                'success' => true,
                'message' => 'PST user deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting PST user: ' . $e->getMessage()
            ], 500);
        }
    }
}
