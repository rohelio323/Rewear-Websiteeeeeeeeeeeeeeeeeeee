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
    public function show(Challenge $challenge)
    {
        // Load the challenge along with all posts submitted to it from the newest
        $challenge->load(['posts' => function($query) {
            $query->latest();
        }, 'posts.user']); 
        
        return view('challenges.show', compact('challenge'));
    }

    // Handle a user joining the challenge and uploading an outfit
    public function submitPost(Request $request, Challenge $challenge)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // 2MB Max
        ]);

        // Save the image securely to the storage folder
        $imagePath = $request->file('image')->store('community_posts', 'public');

        // Create the post using the database columns
        Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'image_path' => $imagePath,
            'users_id' => auth()->id(),
            'challanges_id' => $challenge->id, // Linked directly to the event
            'upvote_count' => 0,
        ]);

        return redirect()->back()->with('success', 'Your outfit has been submitted to the challenge!');
    }
}