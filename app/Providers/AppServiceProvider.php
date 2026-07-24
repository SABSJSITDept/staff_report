<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $employeesQuery = \App\Models\EmployeeOfTheMonth::with(['staff', 'office'])
                    ->where('year', now()->year);

                if ($user->role === 'sanyojak') {
                    $sanyojak = $user->sanyojak;
                    $assignedStaffIds = $sanyojak && $sanyojak->staff_assigned ? (is_string($sanyojak->staff_assigned) ? json_decode($sanyojak->staff_assigned, true) : (array)$sanyojak->staff_assigned) : [];
                    $officeIds = \App\Models\Staff\StaffModel::whereIn('id', $assignedStaffIds)->pluck('office_id')->unique()->toArray();
                    $employeesQuery->whereIn('office_id', $officeIds ?: [0]);
                } elseif ($user->role === 'staff' && $user->staff) {
                    $employeesQuery->where('office_id', $user->staff->office_id);
                }

                $employeesOfTheMonth = $employeesQuery->orderByDesc('month')->get();
                $featuredEmployee = $employeesOfTheMonth->first();
                $otherEmployees = $employeesOfTheMonth->slice(1);

                $view->with(compact('featuredEmployee', 'otherEmployees'));
            }
        });
    }
}
