<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CO2Controller extends Controller
{
    /**
     * [PBI-01] ADMIN: Create a new category and define its CO2 constant with scientific references
     */
    public function addCategory(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:45|unique:categories,category_name',
            'co2_constant' => 'required|numeric|min:0',
            'reference_note' => 'nullable|string',
            'reference_url' => 'nullable|url|max:255',
        ]);

        Category::create([
            'category_name' => $request->category_name,
            'co2_constant' => $request->co2_constant,
            'reference_note' => $request->reference_note,
            'reference_url' => $request->reference_url,
        ]);

        // Redirect back to the dashboard with a success message
        return redirect()->back()->with('success', 'Category and CO2 constant defined successfully with references!');
    }

    /**
     * [PBI-01] ADMIN: Update an existing CO2 constant and its references
     */
    public function updateCategoryCO2(Request $request, $id)
    {
        $request->validate([
            'co2_constant' => 'required|numeric|min:0',
            'reference_note' => 'nullable|string',
            'reference_url' => 'nullable|url|max:255',
        ]);

        $category = Category::find($id);

        if (!$category) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        $category->co2_constant = $request->co2_constant;
        $category->reference_note = $request->reference_note;
        $category->reference_url = $request->reference_url;
        $category->save();

        return redirect()->back()->with('success', 'CO2 constant and references updated successfully!');
    }

    /**
     * [PBI-01] ADMIN: Delete a category
     */
    public function deleteCategory($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully!');
    }
}