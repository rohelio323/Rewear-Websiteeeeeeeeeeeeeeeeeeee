<nav class="fixed top-0 w-full z-50 bg-stone-50/80 backdrop-blur-md border-b">
    <div class="flex justify-between items-center w-full px-6 py-4 max-w-screen-2xl mx-auto">

        <div class="flex items-center gap-8">
            <a href="{{ url('/') }}" class="text-2xl font-bold tracking-tighter text-emerald-900 font-headline">ReWear</a>
            <div class="hidden md:flex gap-6">
                <a href="{{ route('marketplace.index') }}" class="{{ request()->is('marketplace') ? 'text-emerald-900 border-b-2 border-emerald-900 font-bold' : 'text-stone-600' }} pb-1 font-headline text-sm tracking-tight transition-all">Marketplace</a>
                <a href="#" class="{{ request()->is('community') ? 'text-emerald-900 border-b-2 border-emerald-900 font-bold' : 'text-stone-600' }} pb-1 font-headline text-sm tracking-tight transition-all">Community</a>
            </div>
        </div>

        <div class="flex items-center gap-4">
            @auth
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="text-[10px] font-bold text-secondary uppercase border border-secondary/30 px-2 py-2 rounded tracking-widest hover:bg-secondary hover:text-white transition-all">Admin Dashboard</a>
                @endif

                {{-- Wishlist Icon --}}
                <a href="{{ route('favorites.index') }}"
                   class="relative flex items-center justify-center w-9 h-9 rounded-full hover:bg-stone-100 transition-all {{ request()->is('favorites') ? 'text-red-500' : 'text-stone-500' }}"
                   title="Wishlist">
                    <span class="material-symbols-outlined text-xl" style="{{ request()->is('favorites') ? 'font-variation-settings: FILL 1' : '' }}">favorite</span>
                </a>

                <div class="flex items-center gap-3 pl-4 border-l border-stone-200">
                    <div class="flex flex-col items-end hidden sm:flex">
                        <span class="text-xs font-bold text-primary leading-none">Hi, {{ explode(' ', Auth::user()->name)[0] }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-[10px] text-stone-400 hover:text-red-600 transition-all font-medium mt-1">Logout</button>
                        </form>
                    </div>

                    <a href="{{ route('profile.edit') }}" class="w-9 h-9 rounded-full bg-stone-100 border border-stone-200 flex items-center justify-center text-primary hover:bg-stone-200 transition-all group overflow-hidden">
                        @if(Auth::user()->profile_photo_path)
                            <img src="{{ Auth::user()->profile_photo_path }}" alt="Profile" class="w-full h-full object-cover">
                        @else
                            <span class="material-symbols-outlined text-xl">account_circle</span>
                        @endif
                    </a>
                </div>
            @else
                <a href="{{ route('login') }}" class="text-sm font-medium text-stone-600 hover:text-primary transition-colors">Login</a>
                <a href="{{ route('register') }}" class="bg-primary text-white px-5 py-2 rounded-full text-xs font-bold shadow-lg shadow-primary/10 hover:opacity-90 transition-all active:scale-95">Join ReWear</a>
            @endauth
        </div>
    </div>
</nav>
