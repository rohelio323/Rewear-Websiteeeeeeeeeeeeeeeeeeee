<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller {

    public function index() {
        $posts = Post::latest()->get();
        return view('community.index', compact('posts'));
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('community_images', 'public');
        }

        Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'image_path' => $imagePath,
            'users_id' => Auth::id(),
            'upvote_count' => 0,
        ]);

        return redirect()->route('community.index');
    }

    public function update(Request $request, $id) {
        $post = Post::findOrFail($id);

        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'title' => $request->title,
            'content' => $request->content,
        ];

        if ($request->hasFile('image')) {
            if ($post->image_path) {
                Storage::disk('public')->delete($post->image_path);
            }
            $data['image_path'] = $request->file('image')->store('community_images', 'public');
        }

        $post->update($data);
        return redirect()->route('community.index');
    }

    public function destroy($id) {
        $post = Post::findOrFail($id);

        if ($post->image_path) {
            Storage::disk('public')->delete($post->image_path);
        }

        $post->delete();
        return redirect()->route('community.index');
    }
}
