<section>
    <header class="mb-6 flex items-start gap-4">
        <div class="bg-red-100 text-red-600 p-2.5 rounded-full shrink-0 mt-1">
            <span class="material-symbols-outlined text-xl leading-none">warning</span>
        </div>
        <div>
            <h2 class="text-lg font-bold text-red-900 font-headline">Danger Zone</h2>
            <p class="mt-1 text-sm text-red-700/80 leading-relaxed">
                Once your account is deleted, all of your pre-loved listings, challenge history, and impact data will be permanently erased.
            </p>
        </div>
    </header>

    <div class="border-t border-red-200/50 pt-5">
        <button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="bg-red-600 text-white px-6 py-2.5 rounded-full text-sm font-bold hover:bg-red-700 transition shadow-sm active:scale-95 flex items-center gap-2 w-max"
        >
            <span class="material-symbols-outlined text-sm">delete_forever</span>
            Delete Account
        </button>
    </div>

    {{-- We keep x-modal so Laravel's built-in Alpine.js logic still functions perfectly --}}
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8 bg-white relative overflow-hidden">
            @csrf
            @method('delete')

            {{-- Subtle Background Decoration --}}
            <div class="absolute -right-6 -top-6 opacity-5 pointer-events-none">
                <span class="material-symbols-outlined text-[150px] text-red-500">warning</span>
            </div>

            <div class="relative z-10">
                <h2 class="text-xl font-extrabold text-stone-900 mb-2 font-headline">
                    Are you absolutely sure?
                </h2>

                <p class="text-sm text-stone-500 mb-8 leading-relaxed">
                    This action cannot be undone. All of your ReWear data will be permanently deleted. Please enter your password to confirm.
                </p>

                <div class="mb-8">
                    <label for="password" class="block text-[11px] font-bold text-stone-500 uppercase tracking-widest mb-2">Confirm Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-800 focus:bg-white focus:ring-2 focus:ring-red-600 focus:border-transparent transition shadow-sm placeholder-stone-400"
                        placeholder="••••••••"
                    />
                    @if($errors->userDeletion->get('password'))
                        <ul class="mt-2 text-xs text-red-600 font-medium list-disc pl-4">
                            @foreach ((array) $errors->userDeletion->get('password') as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-4 border-t border-stone-100">
                    <button type="button" x-on:click="$dispatch('close')" class="w-full sm:w-auto px-6 py-2.5 rounded-full text-sm font-bold text-stone-500 hover:bg-stone-100 transition">
                        Cancel
                    </button>

                    <button type="submit" class="w-full sm:w-auto bg-red-600 text-white px-6 py-2.5 rounded-full text-sm font-bold hover:bg-red-700 transition shadow-sm active:scale-95">
                        Permanently Delete
                    </button>
                </div>
            </div>
        </form>
    </x-modal>
</section>