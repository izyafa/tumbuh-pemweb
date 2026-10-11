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
    <header class="border-b border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
            <a href="/" class="flex items-center space-x-2">
                <div class="w-7 h-7 rounded-md bg-lime-600 flex items-center justify-center text-white font-bold text-sm">
                    T
                </div>
                <div class="leading-none">
                    <span class="font-bold text-slate-900 tracking-tight text-base">TUMBUH</span>
                    <span class="text-[9px] text-slate-400 font-medium uppercase tracking-wider block">Posyandu Digital</span>
                </div>
            </a>

            <div class="flex items-center space-x-3 text-xs">
                <a href="{{ route('kalkulator.index') }}" class="text-slate-600 hover:text-lime-700 font-medium flex items-center space-x-1">
                    <i data-lucide="calculator" class="w-3.5 h-3.5"></i>
                    <span>Kalkulator</span>
                </a>
                <a href="{{ route('edukasi.index') }}" class="text-slate-600 hover:text-lime-700 font-medium flex items-center space-x-1">
                    <i data-lucide="book-open" class="w-3.5 h-3.5"></i>
                    <span>Edukasi</span>
                </a>
                @auth
                    <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('kader.dashboard') }}" class="px-3 py-1.5 rounded-md bg-lime-600 text-white font-medium hover:bg-lime-700 transition">
                        Buka Dashboard
                    </a>
                @else
                    @unless(request()->routeIs('login'))
                        <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-md border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 font-medium transition">
                            Masuk Petugas
                        </a>
                    @endunless
                @endauth
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
            <div class="flex items-center space-x-4 text-slate-500 font-medium">
                <span>SDG 2: Zero Hunger</span>
                <span>•</span>
                <span>SDG 3: Good Health and Well-Being</span>
            </div>
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
