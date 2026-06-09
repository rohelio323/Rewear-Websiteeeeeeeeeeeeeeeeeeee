<section>
    <header class="mb-6">
        <h2 class="text-lg font-bold text-emerald-900 font-headline">Update Password</h2>
        <p class="mt-1 text-sm text-stone-500 leading-relaxed">Ensure your account is using a long, random password to stay secure.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        {{-- Current Password --}}
        <div>
            <label for="update_password_current_password" class="block text-[11px] font-bold text-stone-500 uppercase tracking-widest mb-2">Current Password</label>
            <input id="update_password_current_password" name="current_password" type="password" 
                   class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-800 focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition shadow-sm placeholder-stone-400" 
                   autocomplete="current-password" placeholder="••••••••" />
            @if ($errors->updatePassword->get('current_password'))
                <ul class="mt-2 text-xs text-red-600 font-medium list-disc pl-4">
                    @foreach ((array) $errors->updatePassword->get('current_password') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- New Password --}}
        <div>
            <label for="update_password_password" class="block text-[11px] font-bold text-stone-500 uppercase tracking-widest mb-2">New Password</label>
            <input id="update_password_password" name="password" type="password" 
                   class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-800 focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition shadow-sm placeholder-stone-400" 
                   autocomplete="new-password" placeholder="••••••••" />
            @if ($errors->updatePassword->get('password'))
                <ul class="mt-2 text-xs text-red-600 font-medium list-disc pl-4">
                    @foreach ((array) $errors->updatePassword->get('password') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="update_password_password_confirmation" class="block text-[11px] font-bold text-stone-500 uppercase tracking-widest mb-2">Confirm Password</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" 
                   class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-800 focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:border-transparent transition shadow-sm placeholder-stone-400" 
                   autocomplete="new-password" placeholder="••••••••" />
            @if ($errors->updatePassword->get('password_confirmation'))
                <ul class="mt-2 text-xs text-red-600 font-medium list-disc pl-4">
                    @foreach ((array) $errors->updatePassword->get('password_confirmation') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Action Area --}}
        <div class="flex items-center gap-4 pt-4 border-t border-stone-100">
            <button type="submit" class="bg-stone-800 text-white px-6 py-2.5 rounded-full text-sm font-bold hover:bg-black transition shadow-sm active:scale-95">
                Update Password
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" 
                   class="text-sm text-emerald-600 font-bold flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base">check_circle</span> 
                    Secure
                </p>
            @endif
        </div>
    </form>
</section>