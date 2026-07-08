<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RatingCategory;
use App\Models\RatingQuestion;
use App\Models\RatingSetting;

class RatingConfigController extends Controller
{
    public function index()
    {
        $categories = RatingCategory::with('questions')->get();
        $setting = RatingSetting::first() ?? new RatingSetting(['is_active' => false, 'financial_year' => '']);
        return view('admin.rating-config.index', compact('categories', 'setting'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'is_active' => 'boolean',
            'financial_year' => 'nullable|string|max:255',
        ]);

        $setting = RatingSetting::first();
        if (!$setting) {
            $setting = new RatingSetting();
        }

        $setting->is_active = $request->has('is_active');
        $setting->financial_year = $request->financial_year;
        $setting->save();

        return redirect()->route('admin.rating-config.index')->with('success', 'Rating settings updated successfully.');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:rating_categories,name',
        ]);

        RatingCategory::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.rating-config.index')->with('success', 'Rating Category added successfully.');
    }

    public function storeQuestion(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:rating_categories,id',
            'question' => 'required|string|max:1000',
        ]);

        RatingQuestion::create([
            'category_id' => $request->category_id,
            'question' => $request->question,
        ]);

        return redirect()->route('admin.rating-config.index')->with('success', 'Question added successfully.');
    }

    public function deleteCategory($id)
    {
        $category = RatingCategory::findOrFail($id);
        
        // This will cascade delete questions if foreign key cascade is set, 
        // else we delete them manually just in case.
        $category->questions()->delete();
        $category->delete();

        return redirect()->route('admin.rating-config.index')->with('success', 'Category and its questions deleted successfully.');
    }

    public function deleteQuestion($id)
    {
        $question = RatingQuestion::findOrFail($id);
        $question->delete();

        return redirect()->route('admin.rating-config.index')->with('success', 'Question deleted successfully.');
    }
}
