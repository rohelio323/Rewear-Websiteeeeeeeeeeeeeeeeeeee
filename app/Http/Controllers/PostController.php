<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller {

    public function index()
    {
        // Fetch all the community posts
        $posts = Post::with('user')->latest()->get(); 

        // Fetch the active challenges
        $activeChallenges = Challenge::where('is_active', true)
            ->where('end_date', '>=', now()->startOfDay())
            ->orderBy('start_date', 'asc')
            ->get();

        // Pass both variables to community view
        return view('community.index', compact('posts', 'activeChallenges'));
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // 2MB Limit
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
            'tags' => $request->tags,
            'upvote_count' => 0,
        ]);

        return redirect()->route('community.index')->with('success', 'Your story has been shared!');
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
            'tags' => $request->tags,
        ];

        if ($request->hasFile('image')) {
            // Delete old image if they upload a new one
            if ($post->image_path) {
                Storage::disk('public')->delete($post->image_path);
            }
            $data['image_path'] = $request->file('image')->store('community_images', 'public');
        }

        $post->update($data);
        
        return redirect()->route('community.index')->with('success', 'Post updated successfully!');
    }

    public function destroy($id) {
        $post = Post::findOrFail($id);

        // Delete the image from storage when the post is deleted
        if ($post->image_path) {
            Storage::disk('public')->delete($post->image_path);
        }

        $post->delete();
        
        return redirect()->route('community.index')->with('success', 'Post deleted successfully.');
    }

    public function hashtagLookup(Request $request)
    {
        $query = $request->query('q');
        
        if (!$query) {
            return response()->json(['challenge' => null]);
        }

        // Search for an active challenge with that exact hashtag
        $challenge = Challenge::where('hashtag', strtolower($query))
            ->where('is_active', true)
            ->where('end_date', '>=', now()->startOfDay())
            ->select('id', 'title', 'hashtag', 'end_date')
            ->first();

        // Format the date if the challenge exists
        if ($challenge) {
            $challenge->end_date_formatted = \Carbon\Carbon::parse($challenge->end_date)->format('M d');
        }

        return response()->json(['challenge' => $challenge]);
    }
}