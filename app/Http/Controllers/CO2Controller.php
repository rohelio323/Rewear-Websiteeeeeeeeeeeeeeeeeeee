<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CO2Controller extends Controller
{
    /**
     * [PBI-01] ADMIN: Create a new category and define its CO2 constant
     */
    public function addCategory(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:45|unique:categories,category_name',
            'co2_constant' => 'required|numeric|min:0'
        ]);

        Category::create([
            'category_name' => $request->category_name,
            'co2_constant' => $request->co2_constant
        ]);

        // Redirect back to the dashboard with a success message
        return redirect()->back()->with('success', 'Category and CO2 constant defined successfully!');
    }

    /**
     * [PBI-01] ADMIN: Update an existing CO2 constant
     */
    public function updateCategoryCO2(Request $request, $id)
    {
        $request->validate([
            'co2_constant' => 'required|numeric|min:0'
        ]);

        $category = Category::find($id);

        if (!$category) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        $category->co2_constant = $request->co2_constant;
        $category->save();

        return redirect()->back()->with('success', 'CO2 constant updated successfully!');
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