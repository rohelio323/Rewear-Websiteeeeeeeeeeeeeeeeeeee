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
        $reports = Report::where('status', 'pending')
            ->with(['reportable', 'reporter'])
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
            // Set status to hidden if the model has that column
            if (isset($reportable->status)) {
                $reportable->update(['status' => 'hidden']);
            }
        }
        $report->update(['status' => 'reviewed']);
        return redirect()->route('admin.moderation.index')
            ->with('success', 'Content hidden successfully.');
    }

    public function delete(Report $report)
    {
        $reportable = $report->reportable;
        if ($reportable) {
            $reportable->delete();
        }
        $report->update(['status' => 'reviewed']);
        return redirect()->route('admin.moderation.index')
            ->with('success', 'Content deleted successfully.');
    }

    public function dismiss(Report $report)
    {
        $report->update(['status' => 'dismissed']);
        return redirect()->route('admin.moderation.index')
            ->with('success', 'Report dismissed.');
    }

    public function warn(Report $report)
    {
        $reportable = $report->reportable;
        if ($reportable) {
            // Get the owner user
            $userId = $reportable->users_id ?? $reportable->user_id ?? null;
            if ($userId) {
                User::where('id', $userId)->increment('warning_count');
            }
        }
        $report->update(['status' => 'reviewed']);
        return redirect()->route('admin.moderation.index')
            ->with('success', 'Warning issued to user.');
    }
}
