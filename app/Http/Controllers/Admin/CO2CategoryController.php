<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category; 

class CO2CategoryController extends Controller
{
    public function index()
    {
        // Fetch all categories from the database
        $categories = Category::all(); 
        
        // Pass to the view
        return view('admin.co2.index', compact('categories'));
    }
}