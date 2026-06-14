@extends('layouts.admin')
@section('title', 'Report Detail')

@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('admin.moderation.index') }}" class="inline-flex items-center gap-2 text-sm text-stone-500 hover:text-emerald-800 transition mb-8">← Back to Queue</a>

    <h1 class="text-2xl font-extrabold text-emerald-950 mb-6">Report Detail</h1>

    <div class="bg-white border border-stone-200 rounded-3xl p-8 shadow-sm mb-6">
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-xs font-bold text-stone-400 uppercase tracking-widest mb-1">Type</p>
                <p class="font-semibold text-stone-900">{{ str_contains($report->reportable_type, 'Item') ? 'Item' : 'Post' }}</p>
            </div>
            <div>
                <p class="text-xs font-bold text-stone-400 uppercase tracking-widest mb-1">Status</p>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest
                    {{ $report->status === 'pending' ? 'bg-red-100 text-red-700' : ($report->status === 'reviewed' ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-100 text-stone-500') }}">
                    {{ $report->status }}
                </span>
            </div>
            <div>
                <p class="text-xs font-bold text-stone-400 uppercase tracking-widest mb-1">Reporter</p>
                <p class="font-semibold text-stone-900">{{ $report->reporter->name ?? 'Unknown' }}</p>
                <p class="text-xs text-stone-400">{{ $report->reporter->email ?? '' }}</p>
            </div>
            <div>
                <p class="text-xs font-bold text-stone-400 uppercase tracking-widest mb-1">Submitted</p>
                <p class="font-semibold text-stone-900">{{ $report->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>

        <div class="mb-6">
            <p class="text-xs font-bold text-stone-400 uppercase tracking-widest mb-2">Reason</p>
            <div class="bg-stone-50 rounded-xl p-4 border border-stone-100">
                <p class="text-sm text-stone-700 leading-relaxed">{{ $report->reason }}</p>
            </div>
        </div>

        <div>
            <p class="text-xs font-bold text-stone-400 uppercase tracking-widest mb-2">Reported Content</p>
            <div class="bg-stone-50 rounded-xl p-4 border border-stone-100">
                @if($report->reportable)
                    @if(str_contains($report->reportable_type, 'Item'))
                        <p class="font-bold text-stone-900">{{ $report->reportable->item_name }}</p>
                        <p class="text-xs text-stone-400 mt-1">Price: Rp {{ number_format($report->reportable->price ?? 0, 0, ',', '.') }}</p>
                    @else
                        <p class="font-bold text-stone-900">{{ $report->reportable->title }}</p>
                        <p class="text-sm text-stone-600 mt-2 leading-relaxed">{{ $report->reportable->content }}</p>
                    @endif
                @else
                    <p class="text-stone-400 italic text-sm">This content has already been deleted.</p>
                @endif
            </div>
        </div>
    </div>

    @if($report->status === 'pending')
        <div class="flex flex-wrap gap-3">
            <form action="{{ route('admin.moderation.hide', $report) }}" method="POST">
                @csrf
                <button class="inline-flex items-center gap-2 px-6 py-3 bg-amber-50 border border-amber-200 text-amber-700 font-bold text-sm rounded-xl hover:bg-amber-100 transition"><span class="material-symbols-outlined text-[16px]">visibility_off</span> Hide Content</button>
            </form>
            <form action="{{ route('admin.moderation.warn', $report) }}" method="POST">
                @csrf
                <button class="inline-flex items-center gap-2 px-6 py-3 bg-orange-50 border border-orange-200 text-orange-700 font-bold text-sm rounded-xl hover:bg-orange-100 transition"><span class="material-symbols-outlined text-[16px]">warning</span> Warn User</button>
            </form>
            <form action="{{ route('admin.moderation.delete', $report) }}" method="POST"
                  onsubmit="return confirm('Permanently delete this content?')">
                @csrf
                <button class="inline-flex items-center gap-2 px-6 py-3 bg-red-50 border border-red-200 text-red-600 font-bold text-sm rounded-xl hover:bg-red-100 transition"><span class="material-symbols-outlined text-[16px]">delete</span> Delete Content</button>
            </form>
            <form action="{{ route('admin.moderation.dismiss', $report) }}" method="POST">
                @csrf
                <button class="inline-flex items-center gap-2 px-6 py-3 bg-stone-50 border border-stone-200 text-stone-600 font-bold text-sm rounded-xl hover:bg-stone-100 transition"><span class="material-symbols-outlined text-[16px]">check</span> Dismiss</button>
            </form>
        </div>
    @endif
</div>
@endsection
