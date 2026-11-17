<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Tumbuh</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Google Font: Roboto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body x-data="{ 
    mobileNav: false, 
    sidebarCollapsed: localStorage.getItem('tumbuh_sidebar_collapsed') === 'true',
    toggleSidebar() {
        this.sidebarCollapsed = !this.sidebarCollapsed;
        localStorage.setItem('tumbuh_sidebar_collapsed', this.sidebarCollapsed);
        this.$nextTick(() => {
            window.dispatchEvent(new Event('resize'));
            if (window.lucide && window.lucide.createIcons) {
                window.lucide.createIcons();
            }
        });
        setTimeout(() => {
            if (window.lucide && window.lucide.createIcons) {
                window.lucide.createIcons();
            }
        }, 50);
    }
}" class="h-full text-slate-800 antialiased font-sans flex flex-col md:flex-row bg-slate-50">

    <!-- Sidebar Navigation -->
    <aside :class="sidebarCollapsed ? 'md:w-16' : 'md:w-64'" 
           class="w-full bg-white border-r border-slate-200 flex flex-col shrink-0">
        <!-- Brand Header -->
        <div class="h-16 border-b border-slate-200 px-3 flex items-center justify-between"
             :class="sidebarCollapsed ? 'md:px-2 md:justify-center' : 'md:px-4'">
            <!-- Mobile Brand View -->
            <div class="flex items-center space-x-2.5 md:hidden">
                <div class="w-8 h-8 rounded-xl bg-lime-50 p-1 border border-lime-200 flex items-center justify-center shadow-xs shrink-0">
                    <img src="{{ asset('images/logo-tumbuh-clean.png') }}" alt="Logo Tumbuh" class="w-full h-full object-contain">
                </div>
                <div>
                    <span class="font-bold text-slate-900 tracking-tight text-lg leading-none block">TUMBUH</span>
                    <span class="text-[10px] text-slate-400 font-medium uppercase tracking-wider block mt-0.5">Posyandu</span>
                </div>
            </div>

            <!-- Desktop Expanded Brand View -->
            <div class="hidden md:flex items-center space-x-2.5 min-w-0" x-show="!sidebarCollapsed" x-cloak>
                <div class="w-8 h-8 rounded-xl bg-lime-50 p-1 border border-lime-200 flex items-center justify-center shadow-xs shrink-0">
                    <img src="{{ asset('images/logo-tumbuh-clean.png') }}" alt="Logo Tumbuh" class="w-full h-full object-contain">
                </div>
                <div class="min-w-0 overflow-hidden">
                    <span class="font-bold text-slate-900 tracking-tight text-lg leading-none block truncate">TUMBUH</span>
                    <span class="text-[10px] text-slate-400 font-medium uppercase tracking-wider block mt-0.5 truncate">Posyandu</span>
                </div>
            </div>

            <!-- Desktop Toggle Button: Ciutkan (ketika terbuka) -->
            <button type="button" 
                    @click="toggleSidebar()" 
                    x-show="!sidebarCollapsed"
                    x-cloak
                    title="Ciutkan Menu"
                    class="hidden md:inline-flex p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-md transition-colors cursor-pointer">
                <i data-lucide="panel-left-close" class="w-4 h-4"></i>
            </button>

            <!-- Desktop Toggle Button: Buka Kembali (ketika menciut / 64px) -->
            <button type="button" 
                    @click="toggleSidebar()" 
                    x-show="sidebarCollapsed"
                    x-cloak
                    title="Buka Menu Navigasi"
                    class="hidden md:flex w-9 h-9 rounded-md bg-lime-50 hover:bg-lime-100 text-lime-700 items-center justify-center transition-colors cursor-pointer group">
                <i data-lucide="panel-left-open" class="w-4 h-4"></i>
            </button>

            <!-- Mobile Hamburger Toggle -->
            <span class="md:hidden">
                <button type="button" @click="mobileNav = !mobileNav" aria-label="Menu navigasi" class="p-1.5 text-slate-500 hover:text-slate-800 rounded-md border border-slate-200">
                    <i data-lucide="menu" class="w-5 h-5" x-show="!mobileNav"></i>
                    <i data-lucide="x" class="w-5 h-5" x-show="mobileNav" x-cloak></i>
                </button>
            </span>
        </div>

        <!-- Collapsible Content for Mobile, Persistent for Desktop -->
        <div :class="mobileNav ? 'flex flex-col' : 'hidden md:flex md:flex-col'" class="flex-1 justify-between overflow-y-auto overflow-x-hidden">
            <!-- Navigation Links -->
            <div class="py-4 space-y-6" :class="sidebarCollapsed ? 'px-2' : 'px-3'">
                <!-- Posyandu Cadre Section -->
                <div>
                    <p x-show="!sidebarCollapsed" x-cloak class="px-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Layanan Posyandu</p>
                    <div x-show="sidebarCollapsed" x-cloak class="h-px bg-slate-100 my-2 mx-1"></div>
                    <nav class="space-y-1">
                        <a href="{{ route('kader.dashboard') }}" 
                           title="Dashboard"
                           class="flex items-center py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('kader.dashboard') ? 'bg-lime-50 text-lime-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : 'space-x-2.5 px-2.5'">
                            <i data-lucide="layout-dashboard" class="w-4 h-4 shrink-0 {{ request()->routeIs('kader.dashboard') ? 'text-lime-700' : 'text-slate-400' }}"></i>
                            <span x-show="!sidebarCollapsed" x-cloak class="truncate">Dashboard</span>
                        </a>

                        <a href="{{ route('kader.measurements.batch') }}" 
                           title="Penimbangan Massal"
                           class="flex items-center py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('kader.measurements.batch') ? 'bg-lime-50 text-lime-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : 'space-x-2.5 px-2.5'">
                            <i data-lucide="scale" class="w-4 h-4 shrink-0 {{ request()->routeIs('kader.measurements.batch') ? 'text-lime-700' : 'text-slate-400' }}"></i>
                            <span x-show="!sidebarCollapsed" x-cloak class="truncate">Penimbangan</span>
                        </a>

                        <a href="{{ route('kader.children.index') }}" 
                           title="Data Balita"
                           class="flex items-center py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('kader.children.*') ? 'bg-lime-50 text-lime-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : 'space-x-2.5 px-2.5'">
                            <i data-lucide="baby" class="w-4 h-4 shrink-0 {{ request()->routeIs('kader.children.*') ? 'text-lime-700' : 'text-slate-400' }}"></i>
                            <span x-show="!sidebarCollapsed" x-cloak class="truncate">Data Balita</span>
                        </a>

                        <a href="{{ route('kader.reports.index') }}" 
                           title="Laporan SKDN"
                           class="flex items-center py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('kader.reports.*') ? 'bg-lime-50 text-lime-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : 'space-x-2.5 px-2.5'">
                            <i data-lucide="file-text" class="w-4 h-4 shrink-0 {{ request()->routeIs('kader.reports.*') ? 'text-lime-700' : 'text-slate-400' }}"></i>
                            <span x-show="!sidebarCollapsed" x-cloak class="truncate">Laporan SKDN</span>
                        </a>

                        <a href="{{ route('kader.broadcast.index') }}" 
                           title="Broadcast WhatsApp"
                           class="flex items-center py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('kader.broadcast.*') ? 'bg-lime-50 text-lime-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : 'space-x-2.5 px-2.5'">
                            <i data-lucide="message-circle-more" class="w-4 h-4 shrink-0 {{ request()->routeIs('kader.broadcast.*') ? 'text-lime-700' : 'text-slate-400' }}"></i>
                            <span x-show="!sidebarCollapsed" x-cloak class="truncate">Broadcast</span>
                        </a>

                        <a href="{{ route('kalkulator.index') }}" 
                           title="Kalkulator Gizi"
                           class="flex items-center py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('kalkulator.*') ? 'bg-lime-50 text-lime-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : 'space-x-2.5 px-2.5'">
                            <i data-lucide="calculator" class="w-4 h-4 shrink-0 {{ request()->routeIs('kalkulator.*') ? 'text-lime-700' : 'text-slate-400' }}"></i>
                            <span x-show="!sidebarCollapsed" x-cloak class="truncate">Kalkulator</span>
                        </a>

                        <a href="{{ route('edukasi.index') }}" 
                           title="Edukasi & Pustaka Video"
                           class="flex items-center py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('edukasi.*') ? 'bg-lime-50 text-lime-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : 'space-x-2.5 px-2.5'">
                            <i data-lucide="graduation-cap" class="w-4 h-4 shrink-0 {{ request()->routeIs('edukasi.*') ? 'text-lime-700' : 'text-slate-400' }}"></i>
                            <span x-show="!sidebarCollapsed" x-cloak class="truncate">Edukasi</span>
                        </a>
                    </nav>
                </div>

                @if(Auth::check() && Auth::user()->isAdmin())
                <!-- Administrator Section -->
                <div>
                    <p x-show="!sidebarCollapsed" x-cloak class="px-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Manajemen Sistem</p>
                    <div x-show="sidebarCollapsed" x-cloak class="h-px bg-slate-100 my-2 mx-1"></div>
                    <nav class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}" 
                           title="Analitik Eksekutif"
                           class="flex items-center py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-lime-50 text-lime-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : 'space-x-2.5 px-2.5'">
                            <i data-lucide="activity" class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-lime-700' : 'text-slate-400' }}"></i>
                            <span x-show="!sidebarCollapsed" x-cloak class="truncate">Analitik</span>
                        </a>

                        <a href="{{ route('admin.users') }}" 
                           title="Manajemen Pengguna"
                           class="flex items-center py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.users') ? 'bg-lime-50 text-lime-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : 'space-x-2.5 px-2.5'">
                            <i data-lucide="users" class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.users') ? 'text-lime-700' : 'text-slate-400' }}"></i>
                            <span x-show="!sidebarCollapsed" x-cloak class="truncate">Pengguna</span>
                        </a>

                        <a href="{{ route('admin.videos') }}" 
                           title="Kelola Video Edukasi"
                           class="flex items-center py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.videos') ? 'bg-lime-50 text-lime-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : 'space-x-2.5 px-2.5'">
                            <i data-lucide="video" class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.videos') ? 'text-lime-700' : 'text-slate-400' }}"></i>
                            <span x-show="!sidebarCollapsed" x-cloak class="truncate">Kelola Edukasi</span>
                        </a>
                    </nav>
                </div>
                @endif
            </div>

            <!-- User Profile & Logout Footbar -->
            <div class="border-t border-slate-200 p-2.5 bg-slate-50/50">
                <div class="flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'justify-between'">
                    <div class="min-w-0 flex-1 pr-2" x-show="!sidebarCollapsed" x-cloak>
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
                        <button type="submit" title="Keluar" class="p-1.5 rounded text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer">
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
