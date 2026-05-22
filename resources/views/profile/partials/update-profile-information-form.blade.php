<section>
    <header class="mb-6">
        <h2 class="text-lg font-bold text-emerald-900 font-headline">Profile Information</h2>
        <p class="mt-1 text-sm text-stone-500 leading-relaxed">Update your account's profile information and email address.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        {{-- Name Input --}}
        <div>
            <label for="name" class="block text-[11px] font-bold text-stone-500 uppercase tracking-widest mb-2">Full Name</label>
            <input id="name" name="name" type="text" 
                   class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-800 font-medium focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition shadow-sm placeholder-stone-400" 
                   value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            @if ($errors->get('name'))
                <ul class="mt-2 text-xs text-red-600 font-medium list-disc pl-4">
                    @foreach ((array) $errors->get('name') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Email Input --}}
        <div>
            <label for="email" class="block text-[11px] font-bold text-stone-500 uppercase tracking-widest mb-2">Email Address</label>
            <input id="email" name="email" type="email" 
                   class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-800 font-medium focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition shadow-sm placeholder-stone-400" 
                   value="{{ old('email', $user->email) }}" required autocomplete="username" />
            @if ($errors->get('email'))
                <ul class="mt-2 text-xs text-red-600 font-medium list-disc pl-4">
                    @foreach ((array) $errors->get('email') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif

            {{-- Unverified Email Notice --}}
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 bg-amber-50 border border-amber-100 rounded-xl p-4">
                    <p class="text-sm text-amber-800 font-medium">
                        Your email address is unverified.
                        <button form="send-verification" class="underline text-amber-600 hover:text-amber-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition">
                            Click here to re-send the verification email.
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium font-sm text-emerald-600 text-xs">
                            A new verification link has been sent to your email address.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Action Area --}}
        <div class="flex items-center gap-4 pt-4 border-t border-stone-100">
            <button type="submit" class="bg-emerald-900 text-white px-6 py-2.5 rounded-full text-sm font-bold hover:bg-emerald-800 transition shadow-sm active:scale-95">
                Save Profile
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" 
                   class="text-sm text-emerald-600 font-bold flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base">check_circle</span> 
                    Saved
                </p>
            @endif
        </div>
    </form>
</section>