<?php

namespace App\Http\Controllers;

use App\Models\ItRecharge;
use App\Models\ItRechargePayment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ItRechargeController extends Controller
{
    public function index()
    {
        $recharges = ItRecharge::orderBy('last_date', 'asc')->get();
        return view('it-management.recharges.index', compact('recharges'));
    }

    public function create()
    {
        return view('it-management.recharges.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'duration_months' => 'required|integer|min:1',
            'billing_day' => 'required|integer|min:1|max:31',
            'amount' => 'required|numeric|min:0',
            'payment_type' => 'required|string|in:Manual,Auto Pay',
            'mode' => 'nullable|string|max:255',
            'details' => 'nullable|string',
        ]);

        // Construct the initial bill date (find the next upcoming date)
        $now = now()->startOfDay();
        $last_date = Carbon::create($now->year, $now->month, $request->billing_day)->startOfDay();
        
        // If the date has already passed this month/year, jump to the next cycle
        if ($last_date->lt($now)) {
            $last_date->addMonths($request->duration_months);
        }
        
        $data = $request->except(['billing_day']);
        $data['last_date'] = $last_date->format('Y-m-d');

        ItRecharge::create($data);

        return redirect()->route('it-management.recharges.index')->with('success', 'Recharge added successfully.');
    }

    public function edit(ItRecharge $recharge)
    {
        return view('it-management.recharges.edit', compact('recharge'));
    }

    public function update(Request $request, ItRecharge $recharge)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'duration_months' => 'required|integer|min:1',
            'billing_day' => 'required|integer|min:1|max:31',
            'amount' => 'required|numeric|min:0',
            'payment_type' => 'required|string|in:Manual,Auto Pay',
            'mode' => 'nullable|string|max:255',
            'details' => 'nullable|string',
        ]);

        $billYear = $recharge->last_date->year;
        $billMonth = $recharge->last_date->month;
        $last_date = Carbon::create($billYear, $billMonth, $request->billing_day);
        
        $data = $request->except(['billing_day']);
        $data['last_date'] = $last_date->format('Y-m-d');

        $recharge->update($data);

        return redirect()->route('it-management.recharges.index')->with('success', 'Recharge updated successfully.');
    }

    public function destroy(ItRecharge $recharge)
    {
        $recharge->delete();
        return redirect()->route('it-management.recharges.index')->with('success', 'Recharge deleted successfully.');
    }

    public function markPaid(Request $request, ItRecharge $recharge)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'paid_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Log the payment
        ItRechargePayment::create([
            'it_recharge_id' => $recharge->id,
            'amount_paid' => $request->amount_paid,
            'paid_at' => $request->paid_at,
            'notes' => $request->notes,
        ]);

        // Extend the last_date by duration_months
        $newDate = Carbon::parse($recharge->last_date)->addMonths($recharge->duration_months);

        $recharge->update([
            'last_date' => $newDate,
            'amount' => $request->amount_paid, // Update amount to the latest one based on user requirement
        ]);

        return redirect()->route('it-management.recharges.index')->with('success', 'Payment marked successfully and next due date updated.');
    }

    public function history(ItRecharge $recharge)
    {
        $payments = $recharge->payments;
        return view('it-management.recharges.history', compact('recharge', 'payments'));
    }
}
