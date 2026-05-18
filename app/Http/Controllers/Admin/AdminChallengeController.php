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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        Challenge::create($validated);

        return redirect()->back()->with('success', 'Challenge created successfully!');
    }
}