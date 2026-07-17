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
        $billing_month = ($request->duration_months == 12 && $request->has('billing_month')) ? $request->billing_month : $now->month;
        $last_date = Carbon::create($now->year, $billing_month, $request->billing_day)->startOfDay();
        
        // If the date has already passed this month/year, jump to the next cycle
        if ($last_date->lt($now)) {
            if ($request->duration_months == 12) {
                $last_date->addYear();
            } else {
                $last_date->addMonths($request->duration_months);
            }
        }
        
        $data = $request->except(['billing_day', 'billing_month']);
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
        $billMonth = ($request->duration_months == 12 && $request->has('billing_month')) ? $request->billing_month : $recharge->last_date->month;
        $last_date = Carbon::create($billYear, $billMonth, $request->billing_day);
        
        $data = $request->except(['billing_day', 'billing_month']);
        $data['last_date'] = $last_date->format('Y-m-d');

        $recharge->update($data);

        return redirect()->route('it-management.recharges.index')->with('success', 'Recharge updated successfully.');
    }

    public function destroy(ItRecharge $recharge)
    {
        $recharge->payments()->delete(); // Cascade delete
        $recharge->delete();
        return redirect()->route('it-management.recharges.index')->with('success', 'Recharge and its payment history deleted successfully.');
    }

    public function markPaid(Request $request, ItRecharge $recharge)
    {
        $request->validate([
            'amount_paid' => 'nullable|numeric|min:0',
            'paid_at' => 'nullable|date',
            'notes' => 'nullable|string',
            'payment_history_csv' => 'nullable|file|mimes:csv,txt|max:2048',
        ]);

        if ($request->hasFile('payment_history_csv')) {
            $file = $request->file('payment_history_csv');
            $handle = fopen($file->path(), 'r');
            $isFirstRow = true;
            
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if ($isFirstRow) {
                    $isFirstRow = false;
                    continue; // Skip header
                }
                
                // Expected columns: Date, Amount, Notes
                if (count($row) >= 2) {
                    try {
                        $rawDate = str_replace('/', '-', trim($row[0]));
                        if (preg_match('/^\d{1,2}-\d{1,2}-\d{2}$/', $rawDate)) {
                            $date = Carbon::createFromFormat('d-m-y', $rawDate)->format('Y-m-d');
                        } elseif (preg_match('/^\d{1,2}-\d{1,2}-\d{4}$/', $rawDate)) {
                            $date = Carbon::createFromFormat('d-m-Y', $rawDate)->format('Y-m-d');
                        } else {
                            $date = Carbon::parse($rawDate)->format('Y-m-d');
                        }

                        $amount = (float) trim($row[1]);
                        $notes = isset($row[2]) ? trim($row[2]) : null;

                        ItRechargePayment::create([
                            'it_recharge_id' => $recharge->id,
                            'amount_paid' => $amount,
                            'paid_at' => $date,
                            'notes' => $notes,
                        ]);
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
            fclose($handle);

            $this->recalculateNextDueDate($recharge);

            return redirect()->route('it-management.recharges.index')->with('success', 'Bulk payment history uploaded and upcoming bill date updated.');
        }

        // Standard single payment logic
        if (!$request->amount_paid || !$request->paid_at) {
            return back()->withErrors(['amount_paid' => 'Amount and date are required for a single payment.']);
        }

        // Log the payment
        ItRechargePayment::create([
            'it_recharge_id' => $recharge->id,
            'amount_paid' => $request->amount_paid,
            'paid_at' => $request->paid_at,
            'notes' => $request->notes,
        ]);

        $recharge->update(['amount' => $request->amount_paid]);
        $this->recalculateNextDueDate($recharge);

        return redirect()->route('it-management.recharges.index')->with('success', 'Payment logged successfully and next due date updated based on history.');
    }

    private function recalculateNextDueDate(ItRecharge $recharge)
    {
        $latestPayment = $recharge->payments()->orderBy('paid_at', 'desc')->first();
        if ($latestPayment) {
            $paidDate = Carbon::parse($latestPayment->paid_at);
            
            // The next due date is exactly duration_months after the latest payment date
            $nextDate = $paidDate->copy()->addMonths($recharge->duration_months);
            
            // We ensure it stays on the original billing day
            $billingDay = Carbon::parse($recharge->last_date)->day;
            if ($recharge->duration_months != 12) {
                $nextDate->day($billingDay);
            } else {
                $billingMonth = Carbon::parse($recharge->last_date)->month;
                $nextDate->month($billingMonth)->day($billingDay);
            }
            
            $recharge->update(['last_date' => $nextDate->format('Y-m-d')]);
        }
    }

    public function close(ItRecharge $recharge)
    {
        $recharge->update(['status' => 'closed']);
        return redirect()->route('it-management.recharges.index')->with('success', 'Recharge marked as closed.');
    }

    public function history(ItRecharge $recharge)
    {
        $payments = $recharge->payments;
        return view('it-management.recharges.history', compact('recharge', 'payments'));
    }
}
