<?php

namespace App\Http\Controllers;

use App\Models\Sanyojak;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Exports\SanyojakExport;
use Maatwebsite\Excel\Facades\Excel;

class SanyojakController extends Controller
{
    public function index()
    {
        $sanyojaks = Sanyojak::with('user')->get()->map(function($s) {
            $s->type = $s->user ? $s->user->role : 'sanyojak';
            return $s;
        });
        return response()->json([
            'success' => true,
            'data' => $sanyojaks
        ]);
    }

    public function export()
    {
        return Excel::download(new SanyojakExport, 'sanyojaks.xlsx');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'pravarti' => 'nullable|string|max:255',
            'email' => 'required|email|unique:sanyojaks,email|unique:users,email',
            'password' => 'required|string|min:6',
            'staff_assigned' => 'nullable|array',
            'type' => 'nullable|string|in:sanyojak,karyalay_sanyojak'
        ]);

        DB::beginTransaction();
        try {
            // Create the User for Login
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['type'] ?? 'sanyojak',
            ]);

            unset($validated['type']);

            // Create Sanyojak
            $validated['password'] = Hash::make($validated['password']);
            $validated['user_id'] = $user->id;
            
            $sanyojak = Sanyojak::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sanyojak created successfully',
                'data' => $sanyojak
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating Sanyojak: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $sanyojak = Sanyojak::find($id);

        if (!$sanyojak) {
            return response()->json([
                'success' => false,
                'message' => 'Sanyojak not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $sanyojak
        ]);
    }

    public function update(Request $request, $id)
    {
        $sanyojak = Sanyojak::find($id);

        if (!$sanyojak) {
            return response()->json([
                'success' => false,
                'message' => 'Sanyojak not found'
            ], 404);
        }

        // Validate user email ignoring this sanyojak's user
        $userId = $sanyojak->user_id;
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'pravarti' => 'nullable|string|max:255',
            'email' => 'sometimes|email|unique:sanyojaks,email,' . $id . '|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:6',
            'staff_assigned' => 'nullable|array',
            'type' => 'nullable|string|in:sanyojak,karyalay_sanyojak'
        ]);

        DB::beginTransaction();
        try {
            // Update User
            if ($userId) {
                $user = User::find($userId);
                if ($user) {
                    if (isset($validated['name'])) $user->name = $validated['name'];
                    if (isset($validated['email'])) $user->email = $validated['email'];
                    if (!empty($validated['password'])) $user->password = Hash::make($validated['password']);
                    if (isset($validated['type'])) $user->role = $validated['type'];
                    $user->save();
                }
            }

            unset($validated['type']);

            // Update Sanyojak
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $sanyojak->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sanyojak updated successfully',
                'data' => $sanyojak
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating Sanyojak: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $sanyojak = Sanyojak::find($id);

        if (!$sanyojak) {
            return response()->json([
                'success' => false,
                'message' => 'Sanyojak not found'
            ], 404);
        }

        DB::beginTransaction();
        try {
            if ($sanyojak->user_id) {
                User::destroy($sanyojak->user_id);
            }
            $sanyojak->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sanyojak deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Sanyojak: ' . $e->getMessage()
            ], 500);
        }
    }
}
