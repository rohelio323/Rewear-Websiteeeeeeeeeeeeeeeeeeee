<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\Post;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    // Show the list of active challenges (The Feed)
    public function index()
    {
        $activeChallenges = Challenge::where('is_active', true)
            ->where('end_date', '>=', now()->startOfDay())
            ->orderBy('start_date', 'asc')
            ->get();

        return view('challenges.index', compact('activeChallenges'));
    }

    // Show a specific challenge and its entries
    public function show($id) {
        $challenge = \App\Models\Challenge::findOrFail($id);
        // Search the tags column using LIKE
        $posts = \App\Models\Post::with('user')->where('tags', 'LIKE', '%' . $challenge->hashtag . '%')->latest()->get();
        return view('challenges.show', compact('challenge', 'posts'));
    }

    // Handle a user joining the challenge and uploading an outfit
    public function submitPost(Request $request, Challenge $challenge) {
        $request->validate([ 'title' => 'required|string|max:255', 'content' => 'required|string', 'image' => 'required|image|mimes:jpeg,png,jpg|max:2048' ]);
        $imagePath = $request->file('image')->store('community_images', 'public');
        
        // Auto-inject the challenge hashtag into the post
        $tags = $request->tags ? $request->tags . ', ' . $challenge->hashtag : $challenge->hashtag;

        Post::create([
            'title' => $request->title, 'content' => $request->content, 'image_path' => $imagePath,
            'users_id' => auth()->id(), 'tags' => $tags, 'upvote_count' => 0,
        ]);

        return redirect()->back()->with('success', 'Your outfit has been submitted!');
    }
}