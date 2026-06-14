<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class AdminModerationController extends Controller
{
    public function index()
    {
        $reports = Report::with(['reportable', 'reporter'])
            ->latest()
            ->paginate(20);

        return view('admin.moderation.index', compact('reports'));
    }

    public function show(Report $report)
    {
        $report->load(['reportable', 'reporter']);
        return view('admin.moderation.show', compact('report'));
    }

    public function hide(Report $report)
    {
        $reportable = $report->reportable;

        if ($reportable) {
            // Items use 'available'/'hidden', Posts now also use 'active'/'hidden'
            if (str_contains($report->reportable_type, 'Item')) {
                 $reportable->update(['status' => 'sold']);
            } elseif (str_contains($report->reportable_type, 'Post')) {
                $reportable->update(['status' => 'hidden']);
            }
        }

        $report->update(['status' => 'reviewed']);

        return redirect()->route('admin.moderation.index')
            ->with('success', 'Content hidden — it will no longer appear publicly.');
    }

    public function delete(Report $report)
    {
        $reportable = $report->reportable;

        if ($reportable) {
            // Clean up photos if it's an Item
            if (str_contains($report->reportable_type, 'Item')) {
                foreach ($reportable->photo_path ?? [] as $path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                }
            }

            // Clean up image if it's a Post
            if (str_contains($report->reportable_type, 'Post')) {
                if ($reportable->image_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($reportable->image_path);
                }
            }

            $reportable->delete();
        }

        $report->update(['status' => 'reviewed']);

        return redirect()->route('admin.moderation.index')
            ->with('success', 'Content permanently deleted.');
    }

    public function dismiss(Report $report)
    {
        $report->update(['status' => 'dismissed']);

        return redirect()->route('admin.moderation.index')
            ->with('success', 'Report dismissed — no action taken.');
    }

    public function warn(Report $report)
    {
        $reportable = $report->reportable;

        if ($reportable) {
            $userId = $reportable->users_id ?? $reportable->user_id ?? null;
            if ($userId) {
                User::where('id', $userId)->increment('warning_count');
            }
        }

        $report->update(['status' => 'reviewed']);

        return redirect()->route('admin.moderation.index')
            ->with('success', 'Warning issued to the content owner.');
    }
}