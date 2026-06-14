<div class="flex items-center justify-between mb-1">
    <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-emerald-600 text-xl">confirmation_number</span>
        <h2 class="text-lg font-bold text-emerald-900">Voucher Exchange</h2>
    </div>
    <span class="text-sm text-stone-500">{{ number_format($totalCo2Saved ?? 0, 1) }} kg CO₂</span>
</div>
<p class="text-sm text-stone-500 mb-4">Redeem your saved CO₂ for rewards.</p>

@if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-medium px-4 py-2 rounded-xl">
        {!! session('success') !!}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-100 text-red-700 text-xs font-medium px-4 py-2 rounded-xl">
        {{ session('error') }}
    </div>
@endif

<div class="flex flex-col gap-2.5">
    @forelse($vouchers as $voucher)
        @php $canAfford = ($totalCo2Saved ?? 0) >= $voucher->co2_cost; @endphp

        <div class="flex items-center justify-between border border-stone-200 rounded-xl px-3 py-2.5 {{ $canAfford ? '' : 'opacity-50' }}">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100 shrink-0">
                    <span class="material-symbols-outlined text-emerald-700 text-lg">{{ $voucher->icon ?? 'redeem' }}</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-stone-800">{{ $voucher->code }}</p>
                    <p class="text-xs text-stone-400">{{ $voucher->co2_cost }} kg CO₂ • Rp {{ number_format($voucher->discount_amount, 0, ',', '.') }}</p>
                </div>
            </div>

            @if($canAfford)
                <form method="POST" action="{{ route('rewards.redeem', $voucher) }}">
                    @csrf
                    <button type="submit" class="text-xs font-bold px-3.5 py-1.5 rounded-lg bg-emerald-900 text-white hover:bg-emerald-800 transition">
                        Redeem
                    </button>
                </form>
            @else
                <button type="button" disabled class="text-xs font-bold px-3.5 py-1.5 rounded-lg bg-stone-100 text-stone-400 cursor-not-allowed">
                    Locked
                </button>
            @endif
        </div>
    @empty
        <p class="text-sm text-stone-400 text-center py-4">No vouchers available right now.</p>
    @endforelse
</div>

<button type="button"
    onclick="document.getElementById('redemption-history-modal').classList.remove('hidden')"
    class="block w-full text-center text-xs font-bold text-emerald-600 mt-4 hover:underline">
    View redemption history
</button>

<div id="redemption-history-modal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center backdrop-blur-sm p-4">
    <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl relative max-h-[80vh] overflow-y-auto">
        <button onclick="document.getElementById('redemption-history-modal').classList.add('hidden')"
            class="absolute top-5 right-6 text-stone-400 hover:text-red-500 text-xl font-bold">✕</button>

        <h2 class="text-xl font-bold text-emerald-950 mb-1">Redemption History</h2>
        <p class="text-xs text-stone-400 mb-5">Vouchers you've redeemed using your saved CO₂.</p>

        <div class="flex flex-col gap-2.5">
            @forelse($redemptions as $redemption)
                <div class="flex items-center justify-between border border-stone-200 rounded-xl px-3 py-2.5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100 shrink-0">
                            <span class="material-symbols-outlined text-emerald-700 text-lg">redeem</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-stone-800">{{ $redemption->voucher->code ?? 'Voucher' }}</p>
                            <p class="text-xs text-stone-400">
                                Rp {{ number_format($redemption->voucher->discount_amount ?? 0, 0, ',', '.') }}
                                · {{ $redemption->created_at->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-emerald-700">-{{ $redemption->co2_deducted }} kg</span>
                </div>
            @empty
                <p class="text-sm text-stone-400 text-center py-4">No redemptions yet.</p>
            @endforelse
        </div>
    </div>
</div>
