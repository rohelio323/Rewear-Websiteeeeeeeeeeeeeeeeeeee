@extends('layouts.admin')
@section('title', 'Moderation Queue')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-end justify-between gap-6 mb-8">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-red-500 mb-1">Admin</p>
            <h1 class="text-3xl font-extrabold text-emerald-950 tracking-tight">Moderation Queue</h1>
        </div>
        <span class="px-4 py-2 bg-red-50 text-red-700 border border-red-200 rounded-xl text-sm font-bold">
            {{ $reports->total() }} pending
        </span>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3 rounded-2xl text-sm">{{ session('success') }}</div>
    @endif

    <div class="space-y-4">
        @forelse($reports as $report)
            <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm">
                <div class="flex flex-col lg:flex-row lg:items-start gap-5">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest
                                {{ str_contains($report->reportable_type, 'Item') ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ str_contains($report->reportable_type, 'Item') ? 'Item' : 'Post' }}
                            </span>
                            <span class="text-xs text-stone-400">{{ $report->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm font-bold text-stone-800 mb-1">
                            Reported by: <span class="text-emerald-800">{{ $report->reporter->name ?? 'Unknown' }}</span>
                        </p>
                        <p class="text-sm text-stone-500 leading-relaxed mb-3">
                            <strong class="text-stone-700">Reason:</strong> {{ $report->reason }}
                        </p>
                        @if($report->reportable)
                            <div class="bg-stone-50 rounded-xl p-4 border border-stone-100">
                                <p class="text-xs text-stone-400 uppercase tracking-widest font-bold mb-1">Reported Content</p>
                                @if(str_contains($report->reportable_type, 'Item'))
                                    <p class="font-semibold text-stone-900 text-sm">{{ $report->reportable->item_name ?? 'Deleted item' }}</p>
                                @else
                                    <p class="font-semibold text-stone-900 text-sm">{{ $report->reportable->title ?? 'Deleted post' }}</p>
                                    @if(isset($report->reportable->content))
                                        <p class="text-xs text-stone-500 mt-1 line-clamp-2">{{ $report->reportable->content }}</p>
                                    @endif
                                @endif
                            </div>
                        @else
                            <div class="bg-stone-50 rounded-xl p-4 border border-stone-100">
                                <p class="text-sm text-stone-400 italic">Content has been deleted.</p>
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-row lg:flex-col gap-2 lg:min-w-[140px]">
                        <form action="{{ route('admin.moderation.hide', $report) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-700 font-bold text-xs rounded-xl transition">
                                🙈 Hide
                            </button>
                        </form>
                        <form action="{{ route('admin.moderation.warn', $report) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-orange-50 hover:bg-orange-100 border border-orange-200 text-orange-700 font-bold text-xs rounded-xl transition">
                                ⚠️ Warn User
                            </button>
                        </form>
                        <form action="{{ route('admin.moderation.delete', $report) }}" method="POST"
                              onsubmit="return confirm('Permanently delete this content?')">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 font-bold text-xs rounded-xl transition">
                                🗑️ Delete
                            </button>
                        </form>
                        <form action="{{ route('admin.moderation.dismiss', $report) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-stone-50 hover:bg-stone-100 border border-stone-200 text-stone-600 font-bold text-xs rounded-xl transition">
                                ✓ Dismiss
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-24 bg-stone-50 rounded-3xl border-2 border-dashed border-stone-200">
                <span class="text-4xl mb-3 block">✅</span>
                <h3 class="font-bold text-stone-700">All clear!</h3>
                <p class="text-stone-400 text-sm mt-1">No pending reports to review.</p>
            </div>
        @endforelse
    </div>
    <div class="mt-6">{{ $reports->links() }}</div>
</div>
@endsection
