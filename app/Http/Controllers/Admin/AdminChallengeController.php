<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use Illuminate\Http\Request;

class AdminChallengeController extends Controller
{
    // Show the list of challenges on the Admin Dashboard
    public function index()
    {
        $challenges = Challenge::latest()->get();
        return view('admin.challenges.index', compact('challenges'));
    }

    // Save a newly created challenge into the database
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'hashtag' => 'required|string|max:50|unique:challenges,hashtag', // <-- Validate it!
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:Active,Draft',
        ]);

        // Clean the hashtag just in case the admin typed a "#" symbol in the input
        $cleanHashtag = str_replace('#', '', $request->hashtag);

        Challenge::create([
            'title' => $request->title,
            'hashtag' => strtolower($cleanHashtag), // Force lowercase for easy matching later
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->status === 'Active',
        ]);

        return redirect()->route('admin.challenges.index')->with('success', 'Challenge created successfully!');
    }
}