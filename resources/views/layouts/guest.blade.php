<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tumbuh') — Platform Pertumbuhan Balita & Gizi</title>

    <!-- Google Font: Roboto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full text-slate-800 antialiased font-sans bg-slate-50 flex flex-col justify-between">

    <!-- Top Minimal Navigation -->
    <header class="border-b border-slate-200 bg-white sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
            <a href="/" class="flex items-center space-x-2">
                <div class="w-7 h-7 rounded-md bg-lime-600 flex items-center justify-center text-white font-bold text-sm shadow-2xs">
                    T
                </div>
                <div class="leading-none">
                    <span class="font-bold text-slate-900 tracking-tight text-base">TUMBUH</span>
                    <span class="text-[9px] text-slate-400 font-medium uppercase tracking-wider block">Posyandu Digital</span>
                </div>
            </a>

            <div class="flex items-center space-x-2 sm:space-x-3 text-xs">
                @if(isset($child) && isset($child->public_token))
                    {{-- Mode Khusus Orang Tua: Pantau KMS, Racik Menu AI, dan Edukasi --}}
                    <a href="{{ route('parent.portal', $child->public_token) }}"
                       class="flex items-center space-x-1.5 font-medium transition px-2.5 py-1.5 rounded-md {{ request()->routeIs('parent.portal') ? 'text-lime-800 font-bold bg-lime-50/80 border border-lime-200' : 'text-slate-600 hover:text-lime-700' }}">
                        <i data-lucide="line-chart" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Pantau KMS</span>
                    </a>

                    <a href="{{ route('parent.menu-gizi', $child->public_token) }}"
                       class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-md text-xs font-bold transition shadow-2xs {{ request()->routeIs('parent.menu-gizi') ? 'bg-lime-700 text-white shadow-xs' : 'bg-lime-600 hover:bg-lime-700 text-white' }}">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                        <span>Racik Menu AI</span>
                    </a>

                    <a href="{{ route('edukasi.index') }}" class="text-slate-600 hover:text-lime-700 font-medium flex items-center space-x-1.5 px-2 py-1.5 rounded-md {{ request()->routeIs('edukasi.*') ? 'text-lime-700 font-bold' : '' }}">
                        <i data-lucide="book-open" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Edukasi</span>
                    </a>
                @elseif(request()->routeIs('login'))
                    {{-- Mode Halaman Login: Bersih dan distraction-free --}}
                @else
                    {{-- Mode Publik Tamu Umum: Kalkulator, Edukasi, dan Masuk Petugas --}}
                    <a href="{{ route('kalkulator.index') }}" class="text-slate-600 hover:text-lime-700 font-medium flex items-center space-x-1 {{ request()->routeIs('kalkulator.*') ? 'text-lime-700 font-bold' : '' }}">
                        <i data-lucide="calculator" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Kalkulator</span>
                    </a>
                    <a href="{{ route('edukasi.index') }}" class="text-slate-600 hover:text-lime-700 font-medium flex items-center space-x-1 {{ request()->routeIs('edukasi.*') ? 'text-lime-700 font-bold' : '' }}">
                        <i data-lucide="book-open" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Edukasi</span>
                    </a>
                    @auth
                        <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('kader.dashboard') }}" class="px-2.5 sm:px-3 py-1.5 rounded-md bg-lime-600 text-white font-medium hover:bg-lime-700 transition">
                            <span class="hidden sm:inline">Buka </span>Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-2.5 sm:px-3 py-1.5 rounded-md border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 font-medium transition">
                            <span class="hidden sm:inline">Masuk </span>Petugas
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- Content Container -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Simple Minimal Footer -->
    <footer class="border-t border-slate-200 bg-white py-4 text-center text-xs text-slate-400">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>Tumbuh &copy; {{ date('Y') }} — Memantau Pertumbuhan Balita Sesuai Standar Kemenkes RI & WHO Anthro.</span>
        </div>
    </footer>

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
