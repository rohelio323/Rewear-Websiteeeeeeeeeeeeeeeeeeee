<?php

namespace App\Http\Controllers;

use App\Models\PostVote;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostVoteController extends Controller
{
    public function vote(Request $request, $id) {
        $request->validate(['value' => 'required|in:1,-1']);

        $post = Post::findOrFail($id);
        $value = (int) $request->value;
        $userId = Auth::id();

        $existing = PostVote::where('post_id', $post->post_id)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            if ($existing->value === $value) {
                $existing->delete();
            } else {
                $existing->update(['value' => $value]);
            }
        } else {
            PostVote::create([
                'post_id' => $post->post_id,
                'user_id' => $userId,
                'value'   => $value,
            ]);
        }

        $score = PostVote::where('post_id', $post->post_id)->sum('value');
        $post->update(['upvote_count' => $score]);

        return redirect()->back();
    }

    public function details($id) {
        $post = Post::findOrFail($id);

        // Security Check: Only allow the post owner (or admin ID 1) to view the breakdown
        if (Auth::id() !== $post->users_id && Auth::id() !== 1) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Aggregate upvotes (value = 1)
        $likes = PostVote::where('post_id', $post->post_id)
            ->where('value', 1)
            ->count();

        // Aggregate downvotes (value = -1)
        $dislikes = PostVote::where('post_id', $post->post_id)
            ->where('value', -1)
            ->count();

        return response()->json([
            'likes' => $likes,
            'dislikes' => $dislikes
        ]);
    }
}
