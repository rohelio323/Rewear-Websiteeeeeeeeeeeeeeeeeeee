<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Order;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $userId = $user->id;

        $totalCo2Saved = $user->total_co2_saved;

        // Calculate Total Score (PBI-23)
        $myScore = Post::where('users_id', $userId)->sum('upvote_count');

        // Calculate Rank (PBI-23)
        $allScores = Post::selectRaw('users_id, SUM(upvote_count) as total_score')
            ->groupBy('users_id')
            ->pluck('total_score', 'users_id');

        $myRank = $allScores->filter(function($score) use ($myScore) {
            return $score > $myScore;
        })->count() + 1;

        // Challenge History (Posts with tags) (PBI-23)
        $challengeHistory = Post::where('users_id', $userId)
            ->whereNotNull('tags')
            ->where('tags', '!=', '')
            ->latest()
            ->take(4) // Just taking the top 4 for the profile page
            ->get();

        // Total Posts count (PBI-23)
        $totalPosts = Post::where('users_id', $userId)->count();

        return view('profile.edit', [
            'user' => $user,
            'totalCo2Saved' => $totalCo2Saved,
            'myScore' => $myScore,
            'myRank' => $myRank,
            'challengeHistory' => $challengeHistory,
            'totalPosts' => $totalPosts,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function applyAsSeller(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user->canApplyAsSeller()) {
            return Redirect::route('profile.edit')->with('error', 'You cannot apply at this time.');
        }

        $user->submitSellerRequest('');

        return Redirect::route('profile.edit')->with('status', 'seller-applied');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}