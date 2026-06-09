<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ReWear | @yield('title', 'Marketplace')</title>

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;700;800&family=Inter:wght@400;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0..1,0" rel="stylesheet"/>
    @stack('styles')
    @vite(['resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary-container": "#2d4739",
                        "on-primary-container": "#98b5a3",
                        "on-secondary-container": "#78361d",
                        "outline": "#727973",
                        "inverse-on-surface": "#f1f1ee",
                        "on-secondary": "#ffffff",
                        "secondary-fixed": "#ffdbcf",
                        "surface": "#f9f9f6",
                        "secondary-fixed-dim": "#ffb59b",
                        "surface-container-high": "#e8e8e5",
                        "surface-container-highest": "#e2e3e0",
                        "on-tertiary-container": "#b8ad97",
                        "on-tertiary": "#ffffff",
                        "surface-tint": "#496455",
                        "on-error-container": "#93000a",
                        "surface-container": "#eeeeeb",
                        "on-error": "#ffffff",
                        "on-tertiary-fixed-variant": "#4d4634",
                        "secondary-container": "#fea181",
                        "surface-dim": "#dadad7",
                        "surface-container-lowest": "#ffffff",
                        "primary": "#173124",
                        "on-secondary-fixed-variant": "#75331b",
                        "error": "#ba1a1a",
                        "on-primary-fixed": "#062014",
                        "on-secondary-fixed": "#380d00",
                        "primary-fixed": "#ccead6",
                        "on-background": "#1a1c1b",
                        "inverse-primary": "#b0cdbb",
                        "tertiary-container": "#484130",
                        "tertiary": "#312b1b",
                        "surface-bright": "#f9f9f6",
                        "primary-fixed-dim": "#b0cdbb",
                        "outline-variant": "#c2c8c2",
                        "on-surface": "#1a1c1b",
                        "inverse-surface": "#2f312f",
                        "on-surface-variant": "#424844",
                        "error-container": "#ffdad6",
                        "on-tertiary-fixed": "#211b0c",
                        "on-primary-fixed-variant": "#324c3e",
                        "surface-variant": "#e2e3e0",
                        "tertiary-fixed-dim": "#d1c5ae",
                        "secondary": "#924a2f",
                        "background": "#f9f9f6",
                        "surface-container-low": "#f4f4f1",
                        "tertiary-fixed": "#ede1c9",
                        "on-primary": "#ffffff"
                    },
                    borderRadius: {
                        DEFAULT: "0.25rem",
                        lg: "0.5rem",
                        xl: "0.75rem",
                        full: "9999px"
                    },
                    fontFamily: {
                        headline: ["Manrope"],
                        body: ["Inter"],
                        label: ["Inter"]
                    }
                }
            }
        }
    </script>

    <style>
      .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        transition: font-variation-settings 0.2s ease;
      }
      .material-symbols-outlined.filled {
        font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      }
      body {
        background-color: #f9f9f6;
        color: #1a1c1b;
        font-family: 'Inter', sans-serif;
      }
    </style>
</head>
<body>

    {{-- Navigation --}}
    @include('layouts.navigation')

    {{-- Page Content --}}
    <main class="pt-24 min-h-screen px-10">
    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-100 border border-emerald-400 text-emerald-800 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 border border-red-400 text-red-700 text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif
    @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="w-full py-12 px-6 mt-20 bg-stone-100 border-t border-stone-200">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 max-w-screen-2xl mx-auto text-sm">
            <div class="space-y-4">
                <span class="font-headline font-bold text-emerald-900 text-xl">ReWear</span>
                <p class="text-stone-500 leading-relaxed">Preserving the history of craftsmanship through circular fashion.</p>
            </div>
            <div class="space-y-4">
                <h4 class="font-bold text-primary uppercase tracking-widest text-xs">Resources</h4>
                <nav class="flex flex-col space-y-2 text-stone-500">
                    <a href="#" class="hover:text-primary">Repair Guides</a>
                    <a href="#" class="hover:text-primary">Sustainability Impact</a>
                </nav>
            </div>
            <div class="space-y-4">
                <h4 class="font-bold text-primary uppercase tracking-widest text-xs">Platform</h4>
                <nav class="flex flex-col space-y-2 text-stone-500">
                    <a href="#" class="hover:text-primary">Terms of Service</a>
                    <a href="#" class="hover:text-primary">Privacy Policy</a>
                </nav>
            </div>
            <div class="space-y-4">
                <h4 class="font-bold text-primary uppercase tracking-widest text-xs">Newsletter</h4>
                <div class="flex gap-2">
                    <input class="bg-white border border-stone-200 rounded-full px-4 py-2 text-xs flex-1 outline-none focus:ring-1 focus:ring-primary" placeholder="email@example.com" type="email"/>
                    <button class="bg-primary text-white px-4 py-2 rounded-full text-xs font-bold">Join</button>
                </div>
            </div>
        </div>
        <div class="mt-12 text-center text-[10px] text-stone-400 font-label uppercase tracking-widest">
            © 2026 ReWear. The Living Archive of Fashion.
        </div>
    </footer>

    @stack('scripts')
</body>
</html>