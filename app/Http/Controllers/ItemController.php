<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index() {
        $items = Item::with(['category', 'user'])->where('status', 'available')->get();
        $categories = Category::all();
        
        if (auth()->check()) {
            auth()->user()->load('favorites');
        }

        return view('marketplace.index', compact('items', 'categories'));
    }
}