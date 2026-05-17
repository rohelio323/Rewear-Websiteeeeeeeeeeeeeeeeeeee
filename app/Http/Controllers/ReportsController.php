<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Item;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    /**
     * PBI-29: Report a marketplace item
     * PBI-30: Report a community post
     * Both handled by one method using polymorphic relationship
     */
    public function store(Request $request)
    {
        // Validate input — only 'item' or 'post' are accepted types
        $request->validate([
            'reportable_type' => 'required|in:item,post',
            'reportable_id'   => 'required|integer',
            'reason'          => 'required|string|max:1000',
        ]);

        // Map the string type to the actual model class
        $modelClass = $request->reportable_type === 'item'
            ? Item::class
            : Post::class;

        // Find the target model
        // NOTE: Post uses 'post_id' as primary key, not 'id'
        // so we can't use findOrFail() directly for posts
        if ($request->reportable_type === 'post') {
            // PBI-30 — find post by post_id
            $model = Post::where('post_id', $request->reportable_id)->firstOrFail();
        } else {
            // PBI-29 — find item by id (standard primary key)
            $model = Item::findOrFail($request->reportable_id);
        }

        // Prevent the same user from submitting duplicate pending reports
        // on the same content — they must wait for the first to be resolved
        $exists = Report::where('reportable_type', $modelClass)
            ->where('reportable_id', $model->getKey())
            ->where('reporter_id', Auth::id())
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already reported this content.');
        }


        Report::create([
            'reportable_type' => $modelClass,
            'reportable_id'   => $model->getKey(),
            'reporter_id'     => Auth::id(),
            'reason'          => $request->reason,
        ]);

        return back()->with('success', 'Report submitted. Our team will review it shortly.');
    }
}
