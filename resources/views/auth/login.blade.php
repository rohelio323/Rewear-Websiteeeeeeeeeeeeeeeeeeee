<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | ReWear</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#173124",
                        "secondary": "#924a2f",
                        "surface": "#f9f9f6",
                        "surface-container-highest": "#e2e3e0",
                        "on-surface": "#1a1c1b",
                        "on-surface-variant": "#424844",
                        "primary-fixed": "#ccead6",
                        "primary-fixed-dim": "#b0cdbb",
                        "error": "#ba1a1a",
                    },
                    fontFamily: {
                        "headline": ["Manrope"],
                        "body": ["Inter"],
                        "label": ["Inter"]
                    }
                },
            },
        }
    </script>
    <style>
        .bg-archive-hero {
            background-image: linear-gradient(rgba(23, 49, 36, 0.4), rgba(23, 49, 36, 0.4)), url('https://images.unsplash.com/photo-1558769132-cb1aea458c5e?q=80&w=2000&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-surface font-body text-on-surface antialiased">

<main class="min-h-screen flex flex-col md:flex-row">
    <section class="hidden md:flex md:w-1/2 lg:w-3/5 bg-archive-hero relative items-center justify-center p-12 overflow-hidden">
        <div class="relative z-10 max-w-xl text-white">
            <div class="mb-8">
                <span class="text-primary-fixed uppercase tracking-[0.2em] font-label text-sm font-semibold">EST. 2024</span>
            </div>
            <h1 class="font-headline font-extrabold text-5xl lg:text-7xl leading-tight tracking-tighter mb-6">
                Welcome Back to the Archive
            </h1>
            <p class="font-body text-lg max-w-md opacity-90">
                Reconnecting you with timeless stories and sustainable fashion heritage. Your next chapter begins here.
            </p>
        </div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-primary/20 blur-[120px] rounded-full"></div>
    </section>

    <section class="flex-1 flex flex-col justify-center items-center px-6 py-12 md:px-12 lg:px-20 bg-surface">
        <div class="w-full max-w-md">
            <div class="md:hidden flex justify-center mb-12">
                <h2 class="text-3xl font-headline font-bold text-primary tracking-tighter">ReWear</h2>
            </div>

            <div class="mb-10 text-center md:text-left">
                <h2 class="text-3xl font-headline font-bold text-primary mb-2">Sign In</h2>
                <p class="text-on-surface-variant font-body">Enter your details to access your curated wardrobe.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="space-y-1.5">
                    <label class="block font-label text-sm font-medium text-on-surface-variant" for="email">Email Address</label>
                    <input class="w-full px-4 py-3 bg-stone-100 border-none rounded-lg focus:ring-2 focus:ring-primary/20 text-on-surface transition-all"
                           id="email" name="email" type="email" value="{{ old('email') }}" required autofocus placeholder="name@archive.com" />
                    @error('email')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5 relative">
                    <div class="flex justify-between items-center">
                        <label class="block font-label text-sm font-medium text-on-surface-variant" for="password">Password</label>
                        @if (Route::has('password.request'))
                            <a class="text-sm font-medium text-secondary hover:underline" href="{{ route('password.request') }}">Forgot password?</a>
                        @endif
                    </div>
                    <input class="w-full px-4 py-3 bg-stone-100 border-none rounded-lg focus:ring-2 focus:ring-primary/20 text-on-surface transition-all"
                           id="password" name="password" type="password" required autocomplete="current-password" placeholder="••••••••" />
                    @error('password')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3">
                    <input class="w-4 h-4 text-primary border-stone-300 rounded focus:ring-primary" id="remember_me" name="remember" type="checkbox"/>
                    <label class="font-label text-sm text-on-surface-variant cursor-pointer" for="remember_me">Keep me signed in</label>
                </div>

                <button class="w-full py-4 bg-primary text-white font-headline font-bold rounded-full shadow-lg hover:bg-emerald-900 transition-all active:scale-[0.98]" type="submit">
                    Sign In
                </button>
            </form>

            <p class="mt-12 text-center text-on-surface-variant font-body text-sm">
                New to the Archive?
                <a class="font-bold text-primary underline underline-offset-4" href="{{ route('register') }}">Join ReWear</a>
            </p>
        </div>
    </section>
</main>

<footer class="md:fixed md:bottom-8 md:right-12 text-center md:text-right p-6 md:p-0">
    <p class="text-[10px] text-stone-400 font-label uppercase tracking-widest">
        © 2024 ReWear. The Living Archive of Fashion.
    </p>
</footer>

</body>
</html>
