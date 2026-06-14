<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        $item->load('user');

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
            'photos'      => 'required|array|min:1|max:4',
            'photos.*'    => 'image|max:5120',
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

    public function edit(Item $item) {
        if (auth()->id() != $item->users_id) {
            abort(403, 'Unauthorized action.');

        }

        $categories = Category::all();

        return view('items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item) {
        if (auth()->id() != $item->users_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'item_name'       => 'required|string|max:255',
            'category_id'     => 'required|exists:categories,id',
            'size'            => 'required|string|max:50',
            'condition'       => 'required|in:new_with_tags,like_new,good,fair',
            'description'     => 'required|string',
            'price'           => 'required|numeric|min:0',
            'existing_photos' => 'nullable|array',
            'photos'          => 'nullable|array|max:4',
            'photos.*'        => 'file|image|max:5120',
        ]);

        $item->update([
            'item_name'   => $validated['item_name'],
            'category_id' => $validated['category_id'],
            'size'        => $validated['size'],
            'condition'   => $validated['condition'],
            'description' => $validated['description'],
            'price'       => $validated['price'],
        ]);

        $existingKept = $request->input('existing_photos', []);  // paths still wanted
        $newPaths     = [];

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $newPaths[] = $file->store('items', 'public');
            }
        }

        $merged = array_values(array_slice(array_merge($existingKept, $newPaths), 0, 4));

        $removed = array_diff($item->photo_path ?? [], $existingKept);
        foreach ($removed as $path) {
            Storage::disk('public')->delete($path);
        }

        $item->update(['photo_path' => $merged]);

        return redirect()->route('items.show', $item)->with('success', 'Listing updated successfully!');
    }

    public function destroy(Item $item) {
        if (auth()->id() != $item->users_id) {
            abort(403, 'Unauthorized action.');
        }

        foreach ($item->photo_path ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }

        $item->delete();

        return redirect()->route('marketplace.index')->with('success', 'Listing deleted successfully.');
    }
}