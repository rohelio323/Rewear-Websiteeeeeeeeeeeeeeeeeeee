@extends('layouts.app')

@section('title', 'Home')

@section('content')

{{-- ─── HERO ─────────────────────────────────────────────────────────────── --}}
<section class="relative rounded-3xl overflow-hidden min-h-[520px] md:min-h-[640px] flex items-center bg-[#f4f4f1] mb-0">
    {{-- Hero Image --}}
    <img
        class="absolute inset-0 w-full h-full object-cover z-0"
        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAR_3xsPhlGWB53S2PVqeC4UBkJwS9InQunzwC-s5jB0bko3Uf_m2fpEXok3OMhg-lry-iFIRSXb66CEg9PTFT-CG7Xj8K3JIko54v1d7XvLjqBCYDth8B_S0vXnHNPrLPIRKix_lqJrSwIvDpfhyTWXwb0GI_o2uKbGlfxgtxWnIidEUjwxJWFnfeYVyPEzz7J7AEkR9zcu7e5j5pGhYkgoGgBfdMZWwYYmiB-HbkU9o79UGRzcRefKS0IRl1vu_jI8pZReCxbf5M"
        alt="Vibrant curated vintage closet"
    />

    {{-- Gradient Overlay --}}
    <div class="absolute inset-0 bg-gradient-to-r from-[#f9f9f6]/95 via-[rgba(249,249,246,0.85)] md:via-[rgba(249,249,246,0.62)] via-55% to-transparent z-10"></div>

    {{-- Content --}}
    <div class="relative z-20 px-8 py-10 md:px-20 md:py-12 max-w-2xl">
        <span class="text-[0.6875rem] font-bold tracking-[0.2em] uppercase text-secondary block mb-4">Let's Go, Sustainable Living</span>

        <h1 class="font-headline text-[2.75rem] md:text-[clamp(3rem,6vw,5rem)] font-extrabold text-primary leading-none tracking-tight mb-6">
            Wear<br>
            <em class="italic text-secondary font-normal">Your Story.</em>
        </h1>

        <p class="text-lg text-[#424844] leading-relaxed max-w-lg mb-12">
            Join the ultimate pre-loved fashion movement. Celebrate every outfit while protecting our planet.
        </p>

        <div class="flex flex-wrap gap-4 mb-10">
            <a href="{{ route('marketplace.index') }}"
               class="bg-primary text-white px-10 py-4 rounded-full text-[0.9375rem] shadow-[0_8px_24px_rgba(23,49,36,0.2)] hover:bg-[#2d4739] active:scale-95 transition-all inline-block font-bold">
                Start Shopping
            </a>
            @auth
                <a href="{{ route('items.create') }}"
                   class="bg-white/80 text-primary border border-primary/10 px-10 py-4 rounded-full font-bold text-[0.9375rem] backdrop-blur hover:bg-white active:scale-95 transition-all inline-block">
                    Sell Collection
                </a>
            @else
                <a href="{{ route('register') }}"
                   class="bg-white/80 text-primary border border-primary/10 px-10 py-4 rounded-full font-bold text-[0.9375rem] backdrop-blur hover:bg-white active:scale-95 transition-all inline-block">
                    Sell Collection
                </a>
            @endauth
        </div>

        {{-- Social Proof --}}
        <div class="flex items-center gap-4">
            <div class="flex">
                <img class="w-10 h-10 rounded-full border-2 border-surface object-cover -ml-2 first:ml-0 shadow-sm"
                     src="https://lh3.googleusercontent.com/aida-public/AB6AXuCR2RL7HQ2iAleeOm8aJD9hY0gJFvDyiYjCaJ7__UkqrZkiyEtTsEV4_OzAVDfgqPEYrjApXtUtnNTeB1WQlWBXz0Z7oawr4tevuTNQmFaCpu7nkjHaFUwPkGSrIL9sE92EFFGFuZPkuDhRRF5iLckKmnJFZgtxB2ecGKsXu06ssQiZQ1DXhrw4rVb0KiKcOiO5P7QqTuxmZOJ2S2tWN6fbqnwC_uy3_jxqz4knz3z3rhZfbiv_32QnbokBS1f02-2ajw3YojiokFw"
                     alt="User"/>
                <img class="w-10 h-10 rounded-full border-2 border-surface object-cover -ml-2 shadow-sm"
                     src="https://lh3.googleusercontent.com/aida-public/AB6AXuBRohckos-OM4-LT2abs-7PaBlEg4qpfLygSEAqirwq14CrLi9gOD7SBNmcybNB-zwqsQHj65OOUXohLco9qEZk8li-ChO3yQoZkbChpDZt30_aN2WyORv_N8jRud_5wda-V_qMcwR8Z729BeCHeaxvWBKPdaz9BtFFJ4d20cy0S70s7PI6zezaS83lhVKCh8CWCwnaaIue42r9Deu16Vp25WyvWZEgXaaDhWTLUVr6yEvIDoSc5DdGKvNaFcLXhLTBpRdr6VAb1Ks"
                     alt="User"/>
                <img class="w-10 h-10 rounded-full border-2 border-surface object-cover -ml-2 shadow-sm"
                     src="https://lh3.googleusercontent.com/aida-public/AB6AXuBeE4kgZPBTIYuGDWhQgPghtow0uyvgex93cxSKpczDdWiZjyO0uVJL_JkFwvOpqyC3qzWWidvFrut67eRkk8DYCk8shc98RqS_hXKY3I4p1dulYr1mtAtSG5obtqhm1u3KawxRsZh0HApkm1W_PnJ4iq9uciySaELdNlda1VYZsb5Ih4hltfkhFsyDUoC9k-Y4yfiKo7jCOPbNCEjRlR-y_8lgbqZnBDDyYdboooTYY3nqorGRFrBwvNFOuyaGBlxcdulcw543dsE"
                     alt="User"/>
            </div>
            <p class="text-sm text-[#424844] font-medium">
                Trusted by <strong class="text-primary font-bold">{{ $userCount ?? 0 }}+ consumers</strong>
            </p>
        </div>
    </div>
</section>

{{-- ─── STATS BAR ─────────────────────────────────────────────────────────── --}}
<section class="max-w-6xl mx-auto -mt-16 px-4 relative z-10 mb-16">
    <div class="bg-white rounded-3xl shadow-[0_15px_50px_-15px_rgba(23,49,36,0.12)] border border-stone-200/60 p-2 md:p-4">
        <div class="grid grid-cols-3 divide-x divide-stone-100">
            <div class="flex flex-col md:flex-row items-center justify-center gap-2 md:gap-4 py-4 md:py-6 px-2 text-center md:text-left">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-2xl bg-emerald-50 text-emerald-800 flex items-center justify-center flex-shrink-0 shadow-sm shadow-emerald-100">
                    <span class="material-symbols-outlined text-xl md:text-2xl font-semibold">groups</span>
                </div>
                <div>
                    <span class="block font-headline font-extrabold text-2xl md:text-3xl text-primary tracking-tight leading-none">{{ $userCount ?? 0 }}+</span>
                    <span class="block text-[10px] md:text-xs font-bold uppercase tracking-wider text-stone-500 mt-1 md:mt-1.5">Members Joined</span>
                </div>
            </div>
            
            <div class="flex flex-col md:flex-row items-center justify-center gap-2 md:gap-4 py-4 md:py-6 px-2 text-center md:text-left">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-2xl bg-emerald-50 text-emerald-800 flex items-center justify-center flex-shrink-0 shadow-sm shadow-emerald-100">
                    <span class="material-symbols-outlined text-xl md:text-2xl font-semibold">checkroom</span>
                </div>
                <div>
                    <span class="block font-headline font-extrabold text-2xl md:text-3xl text-primary tracking-tight leading-none">{{ $itemCount ?? 0 }}</span>
                    <span class="block text-[10px] md:text-xs font-bold uppercase tracking-wider text-stone-500 mt-1 md:mt-1.5">Items Listed</span>
                </div>
            </div>
            
            <div class="flex flex-col md:flex-row items-center justify-center gap-2 md:gap-4 py-4 md:py-6 px-2 text-center md:text-left">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-2xl bg-emerald-50 text-emerald-800 flex items-center justify-center flex-shrink-0 shadow-sm shadow-emerald-100">
                    <span class="material-symbols-outlined text-xl md:text-2xl font-semibold">eco</span>
                </div>
                <div>
                    <span class="block font-headline font-extrabold text-2xl md:text-3xl text-primary tracking-tight leading-none">{{ $orderCount ?? 0 }}</span>
                    <span class="block text-[10px] md:text-xs font-bold uppercase tracking-wider text-stone-500 mt-1 md:mt-1.5">Clothes Rehomed</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─── HOW IT WORKS ───────────────────────────────────────────────────────── --}}
<section class="mb-24 px-4 max-w-6xl mx-auto py-16 bg-[#f4f4f1] rounded-3xl border border-stone-200/40 relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-emerald-50/40 blur-3xl"></div>
    <div class="absolute -bottom-12 -left-12 w-48 h-48 rounded-full bg-orange-50/40 blur-3xl"></div>

    <div class="text-center max-w-2xl mx-auto mb-16 relative z-10">
        <span class="text-[0.6875rem] font-extrabold tracking-[0.25em] uppercase text-secondary bg-orange-50 border border-orange-200/60 px-3 py-1 rounded-full inline-block mb-4">
            How it works
        </span>
        <h2 class="font-headline text-3xl md:text-4xl font-extrabold text-primary tracking-tight leading-tight">
            From one closet to another
        </h2>
        <p class="text-sm md:text-base text-stone-500 mt-3 font-normal max-w-md mx-auto leading-relaxed">
            Buy, sell, or trade clothes with other members in three simple steps.
        </p>
    </div>

    <!-- Timeline Wrapper -->
    <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-10">
        <!-- Connecting Line for Desktop (Hidden on mobile) -->
        <div class="hidden md:block absolute top-[28%] left-[12%] right-[12%] h-[1.5px] border-t-2 border-dashed border-stone-300 -z-10"></div>

        <!-- Step 1 -->
        <div class="bg-white rounded-2xl p-8 border border-stone-200/50 shadow-[0_4px_20px_-2px_rgba(23,49,36,0.02)] hover:shadow-[0_15px_35px_-5px_rgba(23,49,36,0.06)] hover:-translate-y-1 transition-all duration-300 flex flex-col relative group">
            <span class="absolute top-4 right-6 font-headline font-extrabold text-6xl text-stone-100 group-hover:text-emerald-50 transition-colors select-none">01</span>
            
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-800 flex items-center justify-center mb-6 shadow-sm shadow-emerald-100">
                <span class="material-symbols-outlined text-xl font-bold">add_a_photo</span>
            </div>
            
            <h3 class="font-headline font-bold text-primary text-xl mb-3">List your clothes</h3>
            <p class="text-stone-500 text-sm leading-relaxed mb-6">
                Take a few photos of clothes you no longer wear. Set your price, fill in details like size and condition, and post them in minutes.
            </p>
            
            <div class="mt-auto pt-4 border-t border-stone-100 flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-emerald-800">
                <span>Listing fees</span>
                <span class="bg-emerald-50 px-2.5 py-0.5 rounded-full">Free</span>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="bg-white rounded-2xl p-8 border border-stone-200/50 shadow-[0_4px_20px_-2px_rgba(23,49,36,0.02)] hover:shadow-[0_15px_35px_-5px_rgba(23,49,36,0.06)] hover:-translate-y-1 transition-all duration-300 flex flex-col relative group md:translate-y-4">
            <span class="absolute top-4 right-6 font-headline font-extrabold text-6xl text-stone-100 group-hover:text-emerald-50 transition-colors select-none">02</span>
            
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-800 flex items-center justify-center mb-6 shadow-sm shadow-emerald-100">
                <span class="material-symbols-outlined text-xl font-bold">shopping_bag</span>
            </div>
            
            <h3 class="font-headline font-bold text-primary text-xl mb-3">Buy clothes</h3>
            <p class="text-stone-500 text-sm leading-relaxed mb-6">
                Explore hundreds of curated pre-loved clothes. Buy directly from other verified members with secure payment systems and built-in buyer protection.
            </p>
            
            <div class="mt-auto pt-4 border-t border-stone-100 flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-emerald-800">
                <span>Secure Checkout</span>
                <span class="bg-emerald-50 px-2.5 py-0.5 rounded-full">Protected</span>
            </div>
        </div>

        <!-- Step 3 -->
        <div class="bg-white rounded-2xl p-8 border border-stone-200/50 shadow-[0_4px_20px_-2px_rgba(23,49,36,0.02)] hover:shadow-[0_15px_35px_-5px_rgba(23,49,36,0.06)] hover:-translate-y-1 transition-all duration-300 flex flex-col relative group">
            <span class="absolute top-4 right-6 font-headline font-extrabold text-6xl text-stone-100 group-hover:text-emerald-50 transition-colors select-none">03</span>
            
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-800 flex items-center justify-center mb-6 shadow-sm shadow-emerald-100">
                <span class="material-symbols-outlined text-xl font-bold">redeem</span>
            </div>
            
            <h3 class="font-headline font-bold text-primary text-xl mb-3">Redeem vouchers</h3>
            <p class="text-stone-500 text-sm leading-relaxed mb-6">
                Every item you keep in circulation cuts down carbon waste. Exchange your accumulated CO₂ points for discount vouchers to save money on future orders.
            </p>
            
            <div class="mt-auto pt-4 border-t border-stone-100 flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-emerald-800">
                <span>Reward Value</span>
                <span class="bg-emerald-50 px-2.5 py-0.5 rounded-full">Discounts</span>
            </div>
        </div>
    </div>
</section>

{{-- ─── FEATURED LISTINGS ──────────────────────────────────────────────────── --}}
<section class="mb-20 px-2">
    <div class="flex items-end justify-between mb-8">
        <div>
            <span class="text-[0.6875rem] font-bold tracking-[0.2em] uppercase text-secondary block mb-2">Just In</span>
            <h2 class="font-headline text-3xl font-extrabold text-primary">Fresh Listings</h2>
        </div>
        <a href="{{ route('marketplace.index') }}"
           class="text-sm font-bold text-primary border-b border-primary/30 hover:border-primary transition-colors pb-0.5 hidden md:block">
            View all →
        </a>
    </div>

    @if($featuredItems->isEmpty())
        <div class="text-center py-20 bg-surface-container-low rounded-2xl">
            <span class="material-symbols-outlined text-5xl text-on-surface-variant mb-4 block">checkroom</span>
            <p class="text-on-surface-variant font-medium">No listings yet — be the first to sell!</p>
            @auth
                <a href="{{ route('items.create') }}"
                   class="mt-4 inline-block bg-primary text-white px-6 py-2.5 rounded-full text-sm font-bold hover:bg-[#2d4739] transition-colors">
                    List an Item
                </a>
            @endauth
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($featuredItems as $item)
                <x-item-card :item="$item" />
            @endforeach
        </div>

        <div class="text-center mt-6 md:hidden">
            <a href="{{ route('marketplace.index') }}"
               class="inline-block text-sm font-bold text-primary border border-primary/20 px-6 py-2.5 rounded-full hover:bg-primary/5 transition-colors">
                View All Listings →
            </a>
        </div>
    @endif
</section>

{{-- ─── ACTIVE CHALLENGES ──────────────────────────────────────────────────── --}}
@if($activeChallenges->isNotEmpty())
<section class="mb-20 px-2">
    <div class="flex items-end justify-between mb-8">
        <div>
            <span class="text-[0.6875rem] font-bold tracking-[0.2em] uppercase text-secondary block mb-2">Community</span>
            <h2 class="font-headline text-3xl font-extrabold text-primary">Active Challenges</h2>
        </div>
        <a href="{{ route('challenges.index') }}"
           class="text-sm font-bold text-primary border-b border-primary/30 hover:border-primary transition-colors pb-0.5 hidden md:block">
            All challenges →
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @foreach($activeChallenges as $challenge)
            <a href="{{ route('challenges.show', $challenge) }}"
               class="group bg-white rounded-2xl p-6 border border-surface-container-high shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col gap-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="w-10 h-10 rounded-xl bg-secondary-fixed flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-secondary text-xl">emoji_events</span>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-secondary bg-secondary-fixed px-2.5 py-1 rounded-full">
                        {{ \Carbon\Carbon::parse($challenge->end_date)->diffForHumans(['parts' => 1, 'short' => true]) }} left
                    </span>
                </div>
                <div>
                    <h3 class="font-headline font-bold text-primary text-base leading-snug mb-1">{{ $challenge->title }}</h3>
                    <p class="text-xs text-on-surface-variant line-clamp-2 leading-relaxed">{{ $challenge->description }}</p>
                </div>
                <div class="flex items-center gap-2 mt-auto pt-2 border-t border-surface-container">
                    <span class="material-symbols-outlined text-sm text-secondary">tag</span>
                    <span class="text-xs font-bold text-secondary">{{ $challenge->hashtag }}</span>
                    @if($challenge->reward_points)
                        <span class="ml-auto text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">
                            🏆 {{ $challenge->reward_points }} pts
                        </span>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- ─── WHY REWEAR (Value Props) ───────────────────────────────────────────── --}}
<section class="mb-20 px-2">
    <div class="bg-primary rounded-3xl px-8 py-14 md:px-16 overflow-hidden relative">
        {{-- Decorative circles --}}
        <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full bg-white/5 pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-10 w-80 h-80 rounded-full bg-white/5 pointer-events-none"></div>

        <div class="relative text-center mb-12">
            <span class="text-[0.6875rem] font-bold tracking-[0.2em] uppercase text-white/50 block mb-3">Why Choose Us</span>
            <h2 class="font-headline text-3xl md:text-4xl font-extrabold text-white">Fashion That Cares</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
            <div class="flex flex-col items-center text-center gap-4">
                <div class="w-14 h-14 rounded-full bg-white/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-2xl">recycling</span>
                </div>
                <h3 class="font-headline font-bold text-white text-lg">Circular Fashion</h3>
                <p class="text-sm text-white/60 leading-relaxed">Give clothes a second life. Every piece you buy or sell reduces textile waste.</p>
            </div>
            <div class="flex flex-col items-center text-center gap-4">
                <div class="w-14 h-14 rounded-full bg-white/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-2xl">payments</span>
                </div>
                <h3 class="font-headline font-bold text-white text-lg">Earn From Your Closet</h3>
                <p class="text-sm text-white/60 leading-relaxed">List your pre-loved pieces and turn unused fashion into real money.</p>
            </div>
            <div class="flex flex-col items-center text-center gap-4">
                <div class="w-14 h-14 rounded-full bg-white/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-2xl">co2</span>
                </div>
                <h3 class="font-headline font-bold text-white text-lg">Track Your CO₂ Impact</h3>
                <p class="text-sm text-white/60 leading-relaxed">See the real environmental difference your sustainable choices make.</p>
            </div>
        </div>
    </div>
</section>

{{-- ─── SELLER CTA ─────────────────────────────────────────────────────────── --}}
@guest
<section class="mb-8 px-2">
    <div class="bg-surface-container-low rounded-3xl p-10 md:p-14 flex flex-col md:flex-row items-center gap-8 border border-surface-container-high">
        <div class="flex-1 text-center md:text-left">
            <span class="text-[0.6875rem] font-bold tracking-[0.2em] uppercase text-secondary block mb-3">For Sellers</span>
            <h2 class="font-headline text-2xl md:text-3xl font-extrabold text-primary mb-3">
                Have Clothes Collecting Dust?
            </h2>
            <p class="text-on-surface-variant leading-relaxed text-sm max-w-md">
                Join thousands of sellers on ReWear. List your pre-loved pieces in minutes and reach buyers who value sustainable fashion.
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 shrink-0">
            <a href="{{ route('register') }}"
               class="bg-primary text-white px-8 py-3.5 rounded-full font-bold text-sm shadow-md hover:bg-[#2d4739] active:scale-95 transition-all text-center">
                Start Selling
            </a>
            <a href="{{ route('marketplace.index') }}"
               class="bg-white text-primary border border-primary/20 px-8 py-3.5 rounded-full font-bold text-sm hover:bg-primary/5 active:scale-95 transition-all text-center">
                Browse First
            </a>
        </div>
    </div>
</section>
@endguest

@endsection