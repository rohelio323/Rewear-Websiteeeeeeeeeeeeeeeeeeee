<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileStatsController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 1. Calculate Total Score (Sum of all upvotes on their posts)
        $myScore = Post::where('users_id', $userId)->sum('upvote_count');

        // 2. Calculate Leaderboard Rank
        // We pull everyone's score, then count how many people have a higher score than us!
        $allScores = Post::selectRaw('users_id, SUM(upvote_count) as total_score')
            ->groupBy('users_id')
            ->pluck('total_score', 'users_id');

        $myRank = $allScores->filter(function($score) use ($myScore) {
            return $score > $myScore;
        })->count() + 1;

        // 3. Challenge History
        // We find all of their posts that have hashtags attached
        $challengeHistory = Post::where('users_id', $userId)
            ->whereNotNull('tags')
            ->where('tags', '!=', '')
            ->latest()
            ->get();

        // 4. Total Posts count
        $totalPosts = Post::where('users_id', $userId)->count();

        return view('profile.stats', compact('myScore', 'myRank', 'challengeHistory', 'totalPosts'));
    }
}