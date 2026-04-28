<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
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


    public function show(Item $item) {
        $similarItems = Item::where('category_id', $item->category_id)
            ->where('id', '!=', $item->id)
            ->where('status', 'available')
            ->limit(4)
            ->get();

        return view('items.show', compact('item', 'similarItems'));
    }

    public function create() {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'item_name'   => 'required|string|max:255',
            'description' => 'required|string',
            'size'        => 'required|string|max:45',
            'condition'   => 'required|in:new_with_tags,like_new,good,fair',
            'price'       => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categories,id',
            'photos'      => 'required|array|min:1|max:5',
            'photos.*'    => 'image|max:2048',
        ]);

        $photoPaths = [];
        foreach ($request->file('photos', []) as $photo) {
            $photoPaths[] = $photo->store('items', 'public');
        }

        $item = Auth::user()->items()->create([
            'item_name'   => $validated['item_name'],
            'description' => $validated['description'],
            'size'        => $validated['size'],
            'condition'   => $validated['condition'],
            'price'       => $validated['price'],
            'category_id' => $validated['category_id'],
            'photo_path'  => $photoPaths,
            'status'      => 'available',
        ]);

        return redirect()->route('marketplace.index', $item)->with('success', 'Listing created successfully!');
    }
}

