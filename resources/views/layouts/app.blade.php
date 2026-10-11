<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Tumbuh</title>

    <!-- Google Font: Roboto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full text-slate-800 antialiased font-sans flex flex-col md:flex-row bg-slate-50">

    <!-- Sidebar Navigation -->
    <aside x-data="{ mobileNav: false }" class="w-full md:w-64 bg-white border-r border-slate-200 flex flex-col shrink-0">
        <!-- Brand Header -->
        <div class="h-16 border-b border-slate-200 px-5 flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-md bg-lime-600 flex items-center justify-center text-white font-bold text-base shadow-sm">
                    T
                </div>
                <div>
                    <span class="font-bold text-slate-900 tracking-tight text-lg leading-none block">TUMBUH</span>
                    <span class="text-[10px] text-slate-400 font-medium uppercase tracking-wider block mt-0.5">Posyandu • SDG 2 & 3</span>
                </div>
            </div>
            <span class="md:hidden">
                <button type="button" @click="mobileNav = !mobileNav" aria-label="Menu navigasi" class="p-1.5 text-slate-500 hover:text-slate-800 rounded-md border border-slate-200">
                    <i data-lucide="menu" class="w-5 h-5" x-show="!mobileNav"></i>
                    <i data-lucide="x" class="w-5 h-5" x-show="mobileNav" x-cloak></i>
                </button>
            </span>
        </div>

        <!-- Collapsible Content for Mobile, Persistent for Desktop -->
        <div :class="mobileNav ? 'flex flex-col' : 'hidden md:flex md:flex-col'" class="flex-1 justify-between overflow-y-auto">
            <!-- Navigation Links -->
            <div class="py-4 px-3 space-y-6">
                <!-- Posyandu Cadre Section -->
                <div>
                    <p class="px-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Layanan Posyandu</p>
                    <nav class="space-y-1">
                        <a href="{{ route('kader.dashboard') }}" 
                           class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('kader.dashboard') ? 'bg-lime-50 text-lime-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i data-lucide="layout-dashboard" class="w-4 h-4 {{ request()->routeIs('kader.dashboard') ? 'text-lime-700' : 'text-slate-400' }}"></i>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('kader.measurements.batch') }}" 
                           class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('kader.measurements.batch') ? 'bg-lime-50 text-lime-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i data-lucide="scale" class="w-4 h-4 {{ request()->routeIs('kader.measurements.batch') ? 'text-lime-700' : 'text-slate-400' }}"></i>
                            <span>Penimbangan</span>
                        </a>

                        <a href="{{ route('kader.children.index') }}" 
                           class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('kader.children.*') ? 'bg-lime-50 text-lime-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i data-lucide="baby" class="w-4 h-4 {{ request()->routeIs('kader.children.*') ? 'text-lime-700' : 'text-slate-400' }}"></i>
                            <span>Data Balita</span>
                        </a>

                        <a href="{{ route('kader.reports.index') }}" 
                           class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('kader.reports.*') ? 'bg-lime-50 text-lime-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i data-lucide="file-text" class="w-4 h-4 {{ request()->routeIs('kader.reports.*') ? 'text-lime-700' : 'text-slate-400' }}"></i>
                            <span>Laporan SKDN</span>
                        </a>

                        <a href="{{ route('kader.broadcast.index') }}" 
                           class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('kader.broadcast.*') ? 'bg-lime-50 text-lime-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i data-lucide="message-circle-more" class="w-4 h-4 {{ request()->routeIs('kader.broadcast.*') ? 'text-lime-700' : 'text-slate-400' }}"></i>
                            <span>Broadcast</span>
                        </a>

                        <a href="{{ route('kalkulator.index') }}" 
                           class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('kalkulator.*') ? 'bg-lime-50 text-lime-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i data-lucide="calculator" class="w-4 h-4 {{ request()->routeIs('kalkulator.*') ? 'text-lime-700' : 'text-slate-400' }}"></i>
                            <span>Kalkulator</span>
                        </a>

                        <a href="{{ route('edukasi.index') }}" 
                           class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('edukasi.*') ? 'bg-lime-50 text-lime-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i data-lucide="graduation-cap" class="w-4 h-4 {{ request()->routeIs('edukasi.*') ? 'text-lime-700' : 'text-slate-400' }}"></i>
                            <span>Edukasi</span>
                        </a>
                    </nav>
                </div>

                @if(Auth::check() && Auth::user()->isAdmin())
                <!-- Administrator Section -->
                <div>
                    <p class="px-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Manajemen Sistem</p>
                    <nav class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-lime-50 text-lime-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i data-lucide="activity" class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-lime-700' : 'text-slate-400' }}"></i>
                            <span>Analitik</span>
                        </a>

                        <a href="{{ route('admin.users') }}" 
                           class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.users') ? 'bg-lime-50 text-lime-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i data-lucide="users" class="w-4 h-4 {{ request()->routeIs('admin.users') ? 'text-lime-700' : 'text-slate-400' }}"></i>
                            <span>Pengguna</span>
                        </a>

                        <a href="{{ route('admin.videos') }}" 
                           class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.videos') ? 'bg-lime-50 text-lime-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i data-lucide="video" class="w-4 h-4 {{ request()->routeIs('admin.videos') ? 'text-lime-700' : 'text-slate-400' }}"></i>
                            <span>Kelola Edukasi</span>
                        </a>
                    </nav>
                </div>
                @endif
            </div>

            <!-- User Profile & Logout Footbar -->
            <div class="border-t border-slate-200 p-3 bg-slate-50/50">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1 pr-2">
                        <p class="text-xs font-semibold text-slate-800 truncate">{{ Auth::user()->name ?? 'User' }}</p>
                        <p class="text-[11px] text-slate-500 truncate flex items-center space-x-1">
                            <span class="inline-block w-1.5 h-1.5 rounded-full {{ Auth::user()?->isAdmin() ? 'bg-purple-500' : 'bg-lime-600' }}"></span>
                            <span>{{ Auth::user()?->isAdmin() ? 'Administrator' : 'Kader Posyandu' }}</span>
                        </p>
                        @if(Auth::user()?->posyandu_name)
                            <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->posyandu_name }}</p>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                        @csrf
                        <button type="submit" title="Keluar" class="p-1.5 rounded text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Container -->
    <main class="flex-1 min-w-0 flex flex-col overflow-y-auto">
        <!-- Top Utility Header -->
        <header class="h-16 border-b border-slate-200 bg-white px-6 flex items-center justify-between shrink-0">
            <div class="flex items-center space-x-3">
                <h1 class="text-base font-semibold text-slate-900">@yield('page_title', 'Tumbuh Platform')</h1>
                @hasSection('page_badge')
                    @yield('page_badge')
                @endif
            </div>
            <div class="flex items-center space-x-3 text-xs text-slate-500">
                <span class="hidden sm:inline font-mono">{{ now()->translatedFormat('l, d F Y') }}</span>
                <span class="px-2 py-0.5 rounded border border-slate-200 bg-slate-50 text-[11px] font-medium text-slate-600">
                    WHO Anthro 2006 Standard
                </span>
            </div>
        </header>

        <!-- Flash Notifications -->
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="flex items-center justify-between p-3 mb-4 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center justify-between p-3 mb-4 rounded-md bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="p-3 mb-4 rounded-md bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                    <div class="flex items-center space-x-2 font-medium mb-1">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                        <span>Periksa input Anda:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 pl-6">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Page Content View -->
        <div class="p-6 flex-1">
            @yield('content')
        </div>
    </main>

    @include('components.delete-confirm-modal')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide && window.lucide.createIcons) {
                window.lucide.createIcons();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
