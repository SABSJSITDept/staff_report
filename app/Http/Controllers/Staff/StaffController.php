<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff\StaffModel;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    public function index(): JsonResponse
    {
        $staff = StaffModel::with(['department', 'office', 'user'])->latest()->get()->map(function ($s) {
            return [
                'id'          => $s->id,
                'name'        => $s->name,
                'f_name'      => $s->f_name,
                'dob'         => $s->dob,
                'mobile'      => $s->mobile,
                'email'       => $s->email,
                'doj'         => $s->doj,
                'dept_id'     => $s->dept_id,
                'dept_name'   => $s->department?->name,
                'designation' => $s->designation,
                'address'     => $s->address,
                'office_id'   => $s->office_id,
                'office_name' => $s->office?->name,
                'photo'       => $s->photo ? asset('storage/' . $s->photo) : null,
                'status'      => $s->status,
                'role'        => $s->user?->role ?? 'staff',
                'left_date'   => $s->left_date,
                'created_at'  => $s->created_at,
                'updated_at'  => $s->updated_at,
            ];
        });

        return response()->json(['success' => true, 'data' => $staff]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'f_name'      => ['required', 'string', 'max:255'],
            'dob'         => ['required', 'date'],
            'mobile'      => ['required', 'digits:10'],
            'email'       => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'doj'         => ['required', 'date'],
            'dept_id'     => ['required', 'exists:departments,id'],
            'designation' => ['required', 'string', 'max:255'],
            'address'     => ['required', 'string'],
            'office_id'   => ['required', 'exists:office_details,id'],
            'photo'       => ['nullable', 'image', 'max:2048'],
            'status'      => ['required', 'in:Active,Inactive'],
            'role'        => ['required', 'in:staff,manager,admin'],
        ]);

        $validated['name']        = strtoupper($validated['name']);
        $validated['f_name']      = strtoupper($validated['f_name']);
        $validated['designation'] = strtoupper($validated['designation']);
        $validated['address']     = strtoupper($validated['address']);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('staff_photos', 'public');
        }

        // Create user account if email is provided
        if (!empty($validated['email'])) {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['mobile']),
                'role'     => $request->input('role', 'staff'),
            ]);
            $validated['user_id'] = $user->id;
        }

        $staff = StaffModel::create($validated);
        $staff->load(['department', 'office']);

        return response()->json([
            'success' => true,
            'message' => 'Staff member successfully add ho gaya! Login credentials bhi create ho gaye.',
            'data'    => $staff,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $s = StaffModel::with(['department', 'office', 'user'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'          => $s->id,
                'name'        => $s->name,
                'f_name'      => $s->f_name,
                'dob'         => $s->dob,
                'mobile'      => $s->mobile,
                'email'       => $s->email,
                'doj'         => $s->doj,
                'dept_id'     => $s->dept_id,
                'dept_name'   => $s->department?->name,
                'designation' => $s->designation,
                'address'     => $s->address,
                'office_id'   => $s->office_id,
                'office_name' => $s->office?->name,
                'photo'       => $s->photo ? asset('storage/' . $s->photo) : null,
                'status'      => $s->status,
                'role'        => $s->user?->role ?? 'staff',
                'left_date'   => $s->left_date,
            ],
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $staff = StaffModel::findOrFail($id);

        // Mark as Left shortcut (left_date action only)
        if ($request->has('action') && $request->input('action') === 'mark_left') {
            $request->validate([
                'left_date' => ['required', 'date'],
            ]);
            $staff->update([
                'left_date' => $request->input('left_date'),
                'status'    => 'Inactive',
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Staff member ko left mark kar diya gaya!',
                'data'    => $staff->fresh(),
            ]);
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'f_name'      => ['required', 'string', 'max:255'],
            'dob'         => ['required', 'date'],
            'mobile'      => ['required', 'digits:10'],
            'email'       => ['nullable', 'email', 'max:255', 'unique:users,email,' . ($staff->user_id ?? 'NULL')],
            'doj'         => ['required', 'date'],
            'dept_id'     => ['required', 'exists:departments,id'],
            'designation' => ['required', 'string', 'max:255'],
            'address'     => ['required', 'string'],
            'office_id'   => ['required', 'exists:office_details,id'],
            'photo'       => ['nullable', 'image', 'max:2048'],
            'status'      => ['required', 'in:Active,Inactive'],
            'role'        => ['required', 'in:staff,manager,admin'],
        ]);

        $validated['name']        = strtoupper($validated['name']);
        $validated['f_name']      = strtoupper($validated['f_name']);
        $validated['designation'] = strtoupper($validated['designation']);
        $validated['address']     = strtoupper($validated['address']);

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($staff->photo) {
                Storage::disk('public')->delete($staff->photo);
            }
            $validated['photo'] = $request->file('photo')->store('staff_photos', 'public');
        }

        // Sync user account
        if (!empty($validated['email'])) {
            if ($staff->user_id) {
                // Update existing linked user
                User::where('id', $staff->user_id)->update([
                    'name'     => $validated['name'],
                    'email'    => $validated['email'],
                    'password' => Hash::make($validated['mobile']),
                    'role'     => $request->input('role', 'staff'),
                ]);
            } else {
                // Create new user if not linked yet
                $user = User::create([
                    'name'     => $validated['name'],
                    'email'    => $validated['email'],
                    'password' => Hash::make($validated['mobile']),
                    'role'     => $request->input('role', 'staff'),
                ]);
                $validated['user_id'] = $user->id;
            }
        } else {
            // Email hata di — delete linked user
            if ($staff->user_id) {
                User::where('id', $staff->user_id)->delete();
                $validated['user_id'] = null;
            }
        }

        $staff->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Staff member successfully update ho gaya!',
            'data'    => $staff->fresh()->load(['department', 'office']),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $staff = StaffModel::findOrFail($id);

        if ($staff->photo) {
            Storage::disk('public')->delete($staff->photo);
        }

        // Delete linked user account
        if ($staff->user_id) {
            User::where('id', $staff->user_id)->delete();
        }

        $staff->delete();

        return response()->json([
            'success' => true,
            'message' => 'Staff member successfully delete ho gaya!',
        ]);
    }
}
