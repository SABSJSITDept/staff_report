<?php

namespace App\Http\Controllers;

use App\Models\StockCategory;
use App\Models\StockItem;
use App\Models\StockItemBrand;
use App\Models\StockAllotment;
use App\Models\StockPurchase;
use App\Models\Staff\StaffModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockManagementController extends Controller
{
    public function purchaseIndex()
    {
        $brands = StockItemBrand::with('item.category')->get();
        $purchases = StockPurchase::with('brand.item.category')->latest()->paginate(10);
        return view('StockManagement.purchases', compact('brands', 'purchases'));
    }

    public function purchaseStore(Request $request)
    {
        $request->validate([
            'brand_id' => 'required|exists:stock_item_brands,id',
            'quantity' => 'required|integer|min:1',
            'purchase_date' => 'required|date',
            'vendor_name' => 'nullable|string',
            'invoice_no' => 'nullable|string',
            'amount' => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($request) {
            $brand = StockItemBrand::findOrFail($request->brand_id);
            $data = $request->all();
            $data['item_id'] = $brand->stock_item_id;
            StockPurchase::create($data);
            $brand->increment('quantity', $request->quantity);
        });

        return back()->with('success', 'New purchase recorded and stock updated');
    }

    public function purchaseUpdate(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'purchase_date' => 'required|date',
            'vendor_name' => 'nullable|string',
            'invoice_no' => 'nullable|string',
            'amount' => 'nullable|numeric',
            'remark' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $purchase = StockPurchase::findOrFail($id);
                $brand = StockItemBrand::lockForUpdate()->findOrFail($purchase->brand_id);
                
                $difference = $request->quantity - $purchase->quantity;
                if ($difference != 0) {
                    if ($difference < 0 && $brand->quantity < abs($difference)) {
                        throw new \Exception("Cannot reduce purchase quantity. It will cause negative stock.");
                    }
                    $brand->increment('quantity', $difference);
                }
                
                $purchase->update($request->only(['quantity', 'purchase_date', 'vendor_name', 'invoice_no', 'amount', 'remark']));
            });
            return back()->with('success', 'Purchase updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function purchaseDestroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $purchase = StockPurchase::findOrFail($id);
                $brand = StockItemBrand::lockForUpdate()->findOrFail($purchase->brand_id);
                
                if ($brand->quantity < $purchase->quantity) {
                    throw new \Exception("Cannot delete purchase. Stock is already consumed.");
                }
                
                $brand->decrement('quantity', $purchase->quantity);
                $purchase->delete();
            });
            return back()->with('success', 'Purchase deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function categoryIndex()
    {
        $categories = StockCategory::all();
        return view('StockManagement.categories', compact('categories'));
    }

    public function categoryStore(Request $request)
    {
        $request->validate(['name' => 'required|unique:stock_categories']);
        StockCategory::create($request->all());
        return back()->with('success', 'Category created successfully');
    }

    public function itemIndex(Request $request)
    {
        $categories = StockCategory::all();
        
        $query = StockItem::with(['category', 'brands'])->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $items = $query->paginate(10)->withQueryString();
        
        return view('StockManagement.items', compact('categories', 'items'));
    }

    public function itemStore(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:stock_categories,id',
            'name' => 'required',
        ]);
        StockItem::create($request->all());
        return back()->with('success', 'Item Type created successfully');
    }

    public function brandStore(Request $request)
    {
        $request->validate([
            'stock_item_id' => 'required|exists:stock_items,id',
            'name' => 'required', // This is brand name
            'quantity' => 'required|integer|min:0',
        ]);
        StockItemBrand::create($request->all());
        return back()->with('success', 'Brand/Category added to Item');
    }

    public function itemUpdate(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:stock_categories,id',
            'name' => 'required',
        ]);

        $item = StockItem::findOrFail($id);
        $item->update($request->only(['category_id', 'name']));

        return back()->with('success', 'Item updated successfully');
    }

    public function itemDestroy($id)
    {
        $item = StockItem::findOrFail($id);
        $item->delete();
        return back()->with('success', 'Item Type and all its brands deleted successfully');
    }

    public function brandUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'quantity' => 'required|integer|min:0',
            'details' => 'nullable|string',
        ]);

        $brand = StockItemBrand::findOrFail($id);
        $brand->update($request->all());

        return back()->with('success', 'Brand updated successfully');
    }

    public function brandDestroy($id)
    {
        $brand = StockItemBrand::findOrFail($id);
        $brand->delete();
        return back()->with('success', 'Brand deleted successfully');
    }

    public function allotmentIndex()
    {
        $user = auth()->user();
        $query = StaffModel::query();
        
        if (!$user->isAdmin() && $user->staff) {
            $query->where('office_id', $user->staff->office_id);
        }
        
        $staffs = $query->get();
        $categories = StockCategory::all();
        $brands = StockItemBrand::with('item.category')->where('quantity', '>', 0)->get();
        return view('StockManagement.allotments', compact('staffs', 'categories', 'brands'));
    }

    public function allotmentHistoryIndex()
    {
        $user = auth()->user();
        $query = StaffModel::whereHas('stockAllotments')
            ->with(['stockAllotments.brand.item.category']);
            
        if (!$user->isAdmin() && $user->staff) {
            $query->where('office_id', $user->staff->office_id);
        }
        
        $staffsWithAllotments = $query->get();
            
        return view('StockManagement.allotment-history', compact('staffsWithAllotments'));
    }

    public function allotmentExportPdf()
    {
        $user = auth()->user();
        $query = StaffModel::whereHas('stockAllotments')
            ->with(['stockAllotments.brand.item.category', 'office']);
            
        if (!$user->isAdmin() && $user->staff) {
            $query->where('office_id', $user->staff->office_id);
        }
        
        $staffs = $query->get();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('StockManagement.exports.allotments-pdf', compact('staffs'));
        return $pdf->download('staff_allotment_report_' . date('Y-m-d') . '.pdf');
    }

    public function allotmentExportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\StaffAllotmentExport, 'staff_allotment_report_' . date('Y-m-d') . '.xlsx');
    }

    public function allotmentStore(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff_details,id',
            'items' => 'required|array|min:1',
            'items.*.brand_id' => 'required|exists:stock_item_brands,id',
            'items.*.quantity' => 'required|integer|min:1',
            'allotment_type' => 'required|in:Permanent,Temporary',
            'return_date' => 'required_if:allotment_type,Temporary|nullable|date',
            'allotment_date' => 'required|date',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Group items by brand_id to prevent concurrency loop issues and sum quantities
                $groupedItems = [];
                foreach ($request->items as $itemData) {
                    $bId = $itemData['brand_id'];
                    if (!isset($groupedItems[$bId])) {
                        $groupedItems[$bId] = 0;
                    }
                    $groupedItems[$bId] += $itemData['quantity'];
                }

                // Check stock with lockForUpdate
                foreach ($groupedItems as $brandId => $totalQuantity) {
                    $brand = StockItemBrand::lockForUpdate()->findOrFail($brandId);
                    if ($brand->quantity < $totalQuantity) {
                        throw new \Exception("Insufficient stock for: " . $brand->item->name . " - " . $brand->name);
                    }
                    $brand->decrement('quantity', $totalQuantity);
                }

                // Create allotments
                foreach ($request->items as $itemData) {
                    $brand = StockItemBrand::findOrFail($itemData['brand_id']);
                    StockAllotment::create([
                        'staff_id' => $request->staff_id,
                        'item_id' => $brand->stock_item_id,
                        'brand_id' => $brand->id,
                        'quantity' => $itemData['quantity'],
                        'allotment_type' => $request->allotment_type,
                        'return_date' => $request->return_date,
                        'allotment_date' => $request->allotment_date,
                        'remark' => $request->remark,
                        'status' => 'Allotted'
                    ]);
                }
            });

            return back()->with('success', 'Stock items allotted successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function allotmentReturn(Request $request, $id)
    {
        $request->validate([
            'return_type' => 'required|in:inventory,dump'
        ]);

        try {
            DB::transaction(function () use ($id, $request) {
                $allotment = StockAllotment::findOrFail($id);
                if ($allotment->status === 'Returned') {
                    throw new \Exception("Item is already returned.");
                }

                $brand = StockItemBrand::lockForUpdate()->findOrFail($allotment->brand_id);
                
                if ($request->return_type === 'inventory') {
                    $brand->increment('quantity', $allotment->quantity);
                } elseif ($request->return_type === 'dump') {
                    // Check if dump_quantity column exists, otherwise fallback to standard return or handle accordingly
                    // Since we added migration, it should exist.
                    $brand->increment('dump_quantity', $allotment->quantity);
                }

                $allotment->update([
                    'status' => 'Returned',
                    'returned_date' => now()
                ]);
            });
            return back()->with('success', 'Item returned to ' . ucfirst($request->return_type) . ' successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function allotmentUpdate(Request $request, $id)
    {
        $request->validate([
            'return_date' => 'nullable|date',
            'remark' => 'nullable|string',
        ]);

        $allotment = StockAllotment::findOrFail($id);
        $allotment->update($request->only(['return_date', 'remark']));

        return back()->with('success', 'Allotment updated successfully');
    }

    public function allotmentDestroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $allotment = StockAllotment::findOrFail($id);
                
                if ($allotment->status !== 'Returned') {
                    $brand = StockItemBrand::lockForUpdate()->findOrFail($allotment->brand_id);
                    $brand->increment('quantity', $allotment->quantity);
                }
                
                $allotment->delete();
            });
            return back()->with('success', 'Allotment deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
