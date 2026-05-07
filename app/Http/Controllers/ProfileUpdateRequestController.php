<?php

namespace App\Http\Controllers;

use App\Models\Staff\ProfileUpdateRequest;
use App\Models\Staff\StaffModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ProfileUpdateRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'staff') {
            abort(403);
        }

        $query = ProfileUpdateRequest::with('staff.user')->where('status', 'pending');

        if ($user->role === 'manager') {
            if (!$user->staff) {
                // If manager has no staff record, they have no allotted office
                $requests = collect(); 
            } else {
                $office_id = $user->staff->office_id;
                $query->whereHas('staff', function ($q) use ($office_id) {
                    $q->where('office_id', $office_id);
                });
                $requests = $query->get();
            }
        } else {
            $requests = $query->get();
        }

        return view('ProfileRequests.index', compact('requests'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'staff') {
            return response()->json(['success' => false, 'message' => 'Only staff can request profile updates'], 403);
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'f_name' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'mobile' => 'nullable|digits:10',
            'email' => 'nullable|email|max:255',
            'doj' => 'nullable|date',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = array_filter($request->only('name', 'f_name', 'dob', 'mobile', 'email', 'doj', 'address'));

        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $name = time().'_'.$user->id.'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/profile_photos/requests');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $image->move($destinationPath, $name);
            $data['photo'] = 'profile_photos/requests/'.$name;
        }

        if (empty($data)) {
            return response()->json(['success' => false, 'message' => 'No data provided to update'], 400);
        }

        ProfileUpdateRequest::create([
            'staff_id' => $user->staff->id,
            'requested_data' => $data,
            'status' => 'pending',
        ]);

        return response()->json(['success' => true, 'message' => 'Profile update request submitted successfully.']);
    }

    public function approve($id)
    {
        $user = Auth::user();
        if ($user->role === 'staff') {
            abort(403);
        }

        $updateRequest = ProfileUpdateRequest::findOrFail($id);

        if ($user->role === 'manager') {
            if (!$user->staff || $updateRequest->staff->office_id !== $user->staff->office_id) {
                abort(403);
            }
        }

        DB::beginTransaction();
        try {
            $updateRequest->status = 'approved';
            $updateRequest->reviewed_by = $user->id;
            $updateRequest->reviewed_at = now();
            $updateRequest->save();

            $staff = $updateRequest->staff;
            $data = $updateRequest->requested_data;

            // Update StaffModel
            $staffUpdate = [];
            if (isset($data['name'])) $staffUpdate['name'] = strtoupper($data['name']);
            if (isset($data['f_name'])) $staffUpdate['f_name'] = strtoupper($data['f_name']);
            if (isset($data['dob'])) $staffUpdate['dob'] = $data['dob'];
            if (isset($data['doj'])) $staffUpdate['doj'] = $data['doj'];
            if (isset($data['email'])) $staffUpdate['email'] = $data['email'];
            if (isset($data['mobile'])) $staffUpdate['mobile'] = $data['mobile'];
            if (isset($data['address'])) $staffUpdate['address'] = strtoupper($data['address']);
            
            if (isset($data['photo'])) {
                // Move from requests to final destination if needed, or just update
                $oldPath = public_path($data['photo']);
                $newName = basename($data['photo']);
                $newPath = public_path('profile_photos/'.$newName);
                
                if (file_exists($oldPath)) {
                    rename($oldPath, $newPath);
                    $staffUpdate['photo'] = 'profile_photos/'.$newName;
                }
            }

            if (!empty($staffUpdate)) {
                $staff->update($staffUpdate);
            }

            // Update User
            if ($staff->user_id) {
                $userUpdate = [];
                if (isset($data['name'])) $userUpdate['name'] = strtoupper($data['name']);
                if (isset($data['email'])) $userUpdate['email'] = $data['email'];
                if (isset($data['mobile']) && empty(User::find($staff->user_id)->email)) {
                    // if email is empty but mobile changed, not sure if we want to change password, but prompt said mobile id as password is not forced anymore.
                }
                
                if (!empty($userUpdate)) {
                    User::where('id', $staff->user_id)->update($userUpdate);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Profile update approved and applied.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error applying update: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        $user = Auth::user();
        if ($user->role === 'staff') {
            abort(403);
        }

        $updateRequest = ProfileUpdateRequest::findOrFail($id);
        
        if ($user->role === 'manager') {
            if (!$user->staff || $updateRequest->staff->office_id !== $user->staff->office_id) {
                abort(403);
            }
        }

        $updateRequest->status = 'rejected';
        $updateRequest->reviewed_by = $user->id;
        $updateRequest->reviewed_at = now();
        $updateRequest->save();

        return redirect()->back()->with('success', 'Profile update rejected.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Current password incorrect.'], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Password updated successfully.']);
    }
}
