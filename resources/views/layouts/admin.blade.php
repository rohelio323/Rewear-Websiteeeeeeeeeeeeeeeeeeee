<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — @yield('title', 'Dashboard') | ReWear</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @stack('styles')

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Manrope', system-ui, sans-serif; }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .admin-sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.625rem 0.875rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            color: #5A6B60;
            transition: background 0.15s, color 0.15s, transform 0.15s;
        }
        .admin-sidebar-link:hover {
            background: #F5F5F2;
            color: #1A2820;
            transform: translateX(2px);
        }
        .admin-sidebar-link.active {
            background: #EBF0EC;
            color: #2D4739;
            font-weight: 600;
        }
        .admin-sidebar-link .icon {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            background: #F0F0EC; flex-shrink: 0;
            font-size: 1.1rem;
            transition: background 0.15s, color 0.15s;
        }
        .admin-sidebar-link.active .icon {
            background: #2D4739;
            color: #fff;
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body style="background:#F7F8F6;display:flex;min-height:100vh;">

{{-- Sidebar --}}
<aside style="width:232px;flex-shrink:0;background:#fff;border-right:1.5px solid #E2E2DE;display:flex;flex-direction:column;padding:1.5rem 0;position:fixed;top:0;left:0;bottom:0;z-index:40;">

    {{-- Brand --}}
    <div style="padding:0 1.25rem 1.5rem;border-bottom:1px solid #F0F0EC;margin-bottom:0.75rem;">
        <a href="{{ route('home') }}" style="text-decoration:none;display:inline-block;margin-bottom:6px;">
            <span style="font-family:'Manrope',sans-serif;font-size:1.375rem;font-weight:800;color:#173124;letter-spacing:-0.02em;">Re</span><span style="font-family:'Manrope',sans-serif;font-size:1.375rem;font-weight:800;color:#D98364;letter-spacing:-0.02em;">Wear</span>
        </a>
        <p style="font-size:0.6875rem;color:#8A9E94;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;margin:0;">Admin Console</p>
        <p style="font-size:0.625rem;color:#AABAB0;letter-spacing:0.08em;text-transform:uppercase;font-weight:600;margin:2px 0 0;">Platform Management</p>
    </div>

    {{-- Nav sections --}}
    <div style="flex:1;padding:0 0.75rem;display:flex;flex-direction:column;gap:2px;overflow-y:auto;">

        @php
            $mainLinks = [
                ['route' => 'admin.dashboard',          'label' => 'Dashboard', 'icon' => 'dashboard'],
                ['route' => 'admin.users.index',         'label' => 'Users',     'icon' => 'group'],

            ];
        @endphp

        @foreach($mainLinks as $link)
            @php $isActive = request()->routeIs($link['route'].'*'); @endphp
            <a href="{{ route($link['route']) }}"
               class="admin-sidebar-link {{ $isActive ? 'active' : '' }}">
                <span class="icon material-symbols-outlined">{{ $link['icon'] }}</span>
                {{ $link['label'] }}
            </a>
        @endforeach

        <p style="font-size:0.625rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#AABAB0;padding:1rem 0.875rem 0.375rem;">Content</p>



    </div>

    {{-- Bottom --}}
    <div style="padding:0.75rem 1rem;border-top:1px solid #F0F0EC;margin-top:1rem;">
        <form method="GET" action="{{ url('/') }}">
            <button type="submit"
                    class="admin-sidebar-link"
                    style="width:100%;border:none;cursor:pointer;background:none;font-family:'Inter',sans-serif;">
                <span class="icon material-symbols-outlined">logout</span>
                Exit Admin
            </button>
        </form>
    </div>

</aside>

{{-- Main Content --}}
<div style="margin-left:232px;flex:1;padding:2.5rem;min-height:100vh;">

    {{-- Flash Messages --}}
    @foreach(['success','error','info'] as $type)
        @if(session($type))
            <div class="flash-base flash-{{ $type }}" style="margin-bottom:1.25rem;">
                {{ session($type) }}
            </div>
        @endif
    @endforeach

    @yield('content')
</div>

@stack('scripts')
</body>
</html>
