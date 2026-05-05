@extends('layouts.app')

@section('content')
<section class="relative rounded-3xl overflow-hidden min-h-[480px] md:min-h-[600px] flex items-center bg-[#f4f4f1] ">
    {{-- Hero Image --}}
    <img
        class="absolute inset-0 w-full h-full object-cover z-0"
        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAR_3xsPhlGWB53S2PVqeC4UBkJwS9InQunzwC-s5jB0bko3Uf_m2fpEXok3OMhg-lry-iFIRSXb66CEg9PTFT-CG7Xj8K3JIko54v1d7XvLjqBCYDth8B_S0vXnHNPrLPIRKix_lqJrSwIvDpfhyTWXwb0GI_o2uKbGlfxgtxWnIidEUjwxJWFnfeYVyPEzz7J7AEkR9zcu7e5j5pGhYkgoGgBfdMZWwYYmiB-HbkU9o79UGRzcRefKS0IRl1vu_jI8pZReCxbf5M"
        alt="Vibrant curated vintage closet"
    />
    
    {{-- Gradient Overlay --}}
    <div class="absolute inset-0 bg-gradient-to-r from-[#f9f9f6]/95 via-[rgba(249,249,246,0.85)] md:via-[rgba(249,249,246,0.62)] via-55% to-transparent z-10"></div>
    
    {{-- Content Container --}}
    <div class="relative z-20 px-8 py-10 md:px-20 md:py-12 max-w-2xl">
        <span class="text-[0.6875rem] font-bold tracking-[0.2em] uppercase text-secondary block mb-4">Let’s Go, Sustainable Living</span>
        
        <h1 class="font-headline text-[2.75rem] md:text-[clamp(3rem,6vw,5rem)] font-extrabold text-primary leading-none tracking-tight mb-6">
            Wear<br>
            <em class="italic text-secondary font-normal">Your Story.</em>
        </h1>
        
        <p class="text-lg text-[#424844] leading-relaxed max-w-lg mb-12">
            Join the ultimate pre-loved fashion movement. Celebrate every outfit while protecting our planet.
        </p>
        
        <div class="flex flex-wrap gap-4 mb-10">
            {{-- Start Shopping Button --}}
            <a href="{{ route('marketplace.index') }}" class="bg-primary text-white px-10 py-4 rounded-full text-[0.9375rem] shadow-[0_8px_24px_rgba(23,49,36,0.2)] hover:bg-[#2d4739] active:scale-95 transition-all inline-block">
                Start Shopping
            </a>

            {{-- Sell Collection Button --}}
            @auth
                {{-- Pointing to protected route; middleware will check if is_verified_seller --}}
                <a href="{{ route('items.create') }}" class="bg-white/80 text-primary border border-primary/10 px-10 py-4 rounded-full font-bold text-[0.9375rem] backdrop-blur hover:bg-white active:scale-95 transition-all inline-block">
                    Sell Collection
                </a>
            @else
                {{-- Not logged in; go to register --}}
                <a href="{{ route('register') }}" class="bg-white/80 text-primary border border-primary/10 px-10 py-4 rounded-full font-bold text-[0.9375rem] backdrop-blur hover:bg-white active:scale-95 transition-all inline-block">
                    Sell Collection
                </a>
            @endauth
        </div>

        {{-- Social Proof Section --}}
        <div class="flex items-center gap-4">
            <div class="flex">
                <img class="w-10 h-10 rounded-full border-2 border-surface object-cover -ml-2 first:ml-0 shadow-sm" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCR2RL7HQ2iAleeOm8aJD9hY0gJFvDyiYjCaJ7__UkqrZkiyEtTsEV4_OzAVDfgqPEYrjApXtUtnNTeB1WQlWBXz0Z7oawr4tevuTNQmFaCpu7nkjHaFUwPkGSrIL9sE92EFFGFuZPkuDhRRF5iLckKmnJFZgtxB2ecGKsXu06ssQiZQ1DXhrw4rVb0KiKcOiO5P7QqTuxmZOJ2S2tWN6fbqnwC_uy3_jxqz4knz3z3rhZfbiv_32QnbokBS1f02-2ajw3YojiokFw" alt="Siti Aminah"/>
                <img class="w-10 h-10 rounded-full border-2 border-surface object-cover -ml-2 shadow-sm" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBRohckos-OM4-LT2abs-7PaBlEg4qpfLygSEAqirwq14CrLi9gOD7SBNmcybNB-zwqsQHj65OOUXohLco9qEZk8li-ChO3yQoZkbChpDZt30_aN2WyORv_N8jRud_5wda-V_qMcwR8Z729BeCHeaxvWBKPdaz9BtFFJ4d20cy0S70s7PI6zezaS83lhVKCh8CWCwnaaIue42r9Deu16Vp25WyvWZEgXaaDhWTLUVr6yEvIDoSc5DdGKvNaFcLXhLTBpRdr6VAb1Ks" alt="Budi Santoso"/>
                <img class="w-10 h-10 rounded-full border-2 border-surface object-cover -ml-2 shadow-sm" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBeE4kgZPBTIYuGDWhQgPghtow0uyvgex93cxSKpczDdWiZjyO0uVJL_JkFwvOpqyC3qzWWidvFrut67eRkk8DYCk8shc98RqS_hXKY3I4p1dulYr1mtAtSG5obtqhm1u3KawxRsZh0HApkm1W_PnJ4iq9uciySaELdNlda1VYZsb5Ih4hltfkhFsyDUoC9k-Y4yfiKo7jCOPbNCEjRlR-y_8lgbqZnBDDyYdboooTYY3nqorGRFrBwvNFOuyaGBlxcdulcw543dsE" alt="Dian Sastro"/>
            </div>
            <p class="text-sm text-[#424844] font-medium">
                {{-- Dynamic count from User::count() --}}
                Trusted by <strong class="text-primary font-bold"> {{ $userCount ?? 0 }} + consumers</strong>
            </p>
        </div>
    </div>
</section>
@endsection