<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();

            // Auto-fill missing daily backup for the last working day
            if (Auth::user()->staff) {
                $checkDate = \Carbon\Carbon::yesterday();
                if ($checkDate->isSunday()) {
                    $checkDate->subDay(); // If yesterday was Sunday, check Saturday instead
                }
                
                $checkDateStr = $checkDate->toDateString();
                $staffId = Auth::user()->staff->id;
                
                if (!\App\Models\SystemBackup::where('staff_id', $staffId)->whereDate('backup_date', $checkDateStr)->exists()) {
                    \App\Models\SystemBackup::create([
                        'staff_id' => $staffId,
                        'backup_date' => $checkDateStr,
                        'status' => 'NO',
                        'remark' => 'NO-FILL',
                    ]);
                }
            }

            return $this->redirectByRole(Auth::user()->role);
        }

        return back()->withErrors([
            'email' => 'Email ya Password galat hai.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectByRole(string $role)
    {
        return match ($role) {
            'admin'    => redirect()->route('admin.dashboard'),
            'manager'  => redirect()->route('manager.dashboard'),
            'sanyojak', 'karyalay_sanyojak' => redirect()->route('daily-report.index'),
            'pst'      => redirect()->route('ratings.report'),
            default    => redirect()->route('staff.dashboard'),
        };
    }
}
