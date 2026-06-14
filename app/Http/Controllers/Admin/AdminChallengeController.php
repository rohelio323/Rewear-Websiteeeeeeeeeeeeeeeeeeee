<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use Illuminate\Http\Request;

class AdminChallengeController extends Controller
{
    public function index()
    {
        $challenges = Challenge::latest()->get();
        return view('admin.challenges.index', compact('challenges'));
    }

    public function store(Request $request)
    {
        $cleanHashtag = strtolower(str_replace('#', '', $request->hashtag));
        $request->merge(['hashtag' => $cleanHashtag]);

        $request->validate([
            'title' => 'required|string|max:255',
            'hashtag' => 'required|string|max:50|unique:challenges,hashtag', 
            'description' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:Active,Draft',
            'reward_points' => 'required|integer|min:0', 
        ], [
            'end_date.after_or_equal' => 'Start date cannot be after end date.',
            'start_date.after_or_equal' => 'Start date cannot be in the past.',
            'hashtag.unique' => 'Hashtag already taken.',
        ]);

        Challenge::create([
            'title' => $request->title,
            'hashtag' => $request->hashtag,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->status === 'Active',
            'reward_points' => $request->reward_points,
        ]);

        return redirect()->route('challenges.index')->with('success', 'Challenge created with CO₂ reward!');
    }

    public function edit($id)
    {
        $challenge = Challenge::findOrFail($id);
        return view('admin.challenges.edit', compact('challenge'));
    }

    public function update(Request $request, $id)
    {
        $challenge = Challenge::findOrFail($id);

        $cleanHashtag = strtolower(str_replace('#', '', $request->hashtag));
        $request->merge(['hashtag' => $cleanHashtag]);

        $request->validate([
            'title' => 'required|string|max:255',
            'hashtag' => 'required|string|max:50|unique:challenges,hashtag,' . $challenge->id, 
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reward_points' => 'required|integer|min:0', 
        ], [
            'end_date.after_or_equal' => 'Start date cannot be after end date.',
            'hashtag.unique' => 'Hashtag already taken.',
        ]);

        $challenge->update([
            'title' => $request->title,
            'hashtag' => $request->hashtag,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active') ? $request->is_active : $challenge->is_active, 
            'reward_points' => $request->reward_points,
        ]);

        return redirect()->route('challenges.index')->with('success', 'Challenge updated successfully!');
    }

    public function destroy($id)
    {
        $challenge = Challenge::findOrFail($id);

        $hasSubmissions = \App\Models\Post::where('tags', 'LIKE', '%' . $challenge->hashtag . '%')->exists();
        if ($hasSubmissions) {
            return redirect()->back()->with('error', 'Cannot delete challenge with existing submissions.');
        }

        $challenge->delete();

        return redirect()->route('challenges.index')->with('success', 'Challenge deleted permanently.');
    }
}