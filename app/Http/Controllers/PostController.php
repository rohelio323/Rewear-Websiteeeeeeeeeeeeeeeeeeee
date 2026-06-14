<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Challenge;
use App\Models\PostVote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PostController extends Controller {

    public function index()
    {
        // 1. Fetch all the community posts with the user data
        $posts = Post::with('user')->where('status', '!=', 'hidden')->latest()->get();

        // 2. Attach the user's vote data to each post
        if (Auth::check()) {
            $userVotes = PostVote::where('user_id', Auth::id())
                ->whereIn('post_id', $posts->pluck('post_id'))
                ->pluck('value', 'post_id');

            foreach($posts as $post) {
                $post->my_vote = $userVotes[$post->post_id] ?? null;
            }
        }

        // 3. Fetch the active challenges
        $activeChallenges = Challenge::where('is_active', true)
            ->where('end_date', '>=', now()->startOfDay())
            ->orderBy('start_date', 'asc')
            ->get();

        $trendingPosts = Post::with('user')
            ->where('status', '!=', 'hidden')
            ->where('created_at', '>=', Carbon::now()->startOfWeek())
            ->orderByDesc('upvote_count')
            ->get();

        $topUser = User::select('users.id', 'users.name')
            ->join('posts', 'users.id', '=', 'posts.users_id')
            ->selectRaw('SUM(posts.upvote_count) as total_upvotes')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_upvotes')
            ->take(5)
            ->get();

        // 4. Pass both variables to community view
        return view('community.index', compact('posts', 'activeChallenges', 'trendingPosts', 'topUser'));
    }

    public function store(Request $request) {
        
        // --- CHALLENGE HASHTAG DETECTION ---
        // Get all active challenge hashtags to check against
        $activeHashtags = Challenge::where('is_active', true)->pluck('hashtag')->toArray();
        $textToCheck = strtolower($request->content . ' ' . $request->tags);
        
        $isChallengePost = false;
        foreach ($activeHashtags as $hashtag) {
            // Check if their content or tags contain the active hashtag
            if (str_contains($textToCheck, strtolower($hashtag))) {
                $isChallengePost = true;
                break;
            }
        }

        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            // Dynamically require image ONLY if a challenge hashtag was found
            'image' => [
                $isChallengePost ? 'required' : 'nullable', 
                'image', 
                'mimes:jpeg,png,jpg', 
                'max:2048'
            ],
        ], [
            // Custom friendly error message so the user knows exactly why it failed
            'image.required' => 'Since you used a challenge hashtag, you must upload a photo of your outfit to participate!'
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

        // Enforce the 30-minute post edit window
        if ($post->created_at->diffInMinutes(now()) >= 30) {
            return redirect()->route('community.index')->with('error', 'This post is outside its 30-minute edit window and can no longer be edited.');
        }

        // --- CHALLENGE HASHTAG DETECTION ---
        $activeHashtags = Challenge::where('is_active', true)->pluck('hashtag')->toArray();
        $textToCheck = strtolower($request->content . ' ' . $request->tags);
        
        $isChallengePost = false;
        foreach ($activeHashtags as $hashtag) {
            if (str_contains($textToCheck, strtolower($hashtag))) {
                $isChallengePost = true;
                break;
            }
        }

        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => [
                // If it's a challenge post, they MUST have an image. 
                // Either they are uploading a new one, or the post already has one in the database.
                ($isChallengePost && !$post->image_path) ? 'required' : 'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048'
            ],
        ], [
            'image.required' => 'Challenge posts require a photo. Please upload one to keep your challenge status!'
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