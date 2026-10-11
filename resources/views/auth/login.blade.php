@extends('layouts.guest')

@section('title', 'Masuk Petugas & Kader')

@section('content')
<div class="min-h-[calc(100vh-120px)] flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md">
        <!-- Logo & Title Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-lime-600 text-white font-bold text-xl mb-3 shadow-sm">
                T
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">Masuk ke Portal Tumbuh</h2>
            <p class="mt-1 text-sm text-slate-500">Sistem Pencatatan Pertumbuhan Balita & Posyandu Digital</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6 sm:p-8">
            @if(session('error'))
                <div class="mb-4 p-3 rounded-md bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center space-x-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 p-3 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center space-x-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Alamat Email</label>
                    <input type="email" name="email" id="email" required autocomplete="email" autofocus
                           value="{{ old('email', 'kader1@tumbuh.id') }}"
                           placeholder="Contoh: kader1@tumbuh.id atau admin@tumbuh.id"
                           class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600 transition">
                    @error('email')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Kata Sandi</label>
                    <input type="password" name="password" id="password" required autocomplete="current-password"
                           value="password"
                           placeholder="Masukkan kata sandi akun"
                           class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600 transition">
                    @error('password')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center space-x-2 text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-lime-600 focus:ring-lime-500">
                        <span>Ingat saya</span>
                    </label>
                </div>

                <button type="submit" 
                        class="w-full py-2.5 px-4 rounded-md bg-lime-600 hover:bg-lime-700 text-white font-medium text-sm transition shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-lime-600 flex items-center justify-center space-x-2">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>Masuk ke Sistem</span>
                </button>
            </form>

            <!-- Quick Demo Credentials Box -->
            <div class="mt-6 pt-5 border-t border-slate-100 text-xs">
                <p class="font-semibold text-slate-600 mb-2">Akun Demo Cepat (Klik untuk mengisi):</p>
                <div class="space-y-1.5 font-mono text-[11px]">
                    <button type="button" 
                            onclick="document.getElementById('email').value='kader1@tumbuh.id'; document.getElementById('password').value='password';"
                            class="w-full text-left p-2 rounded bg-slate-50 hover:bg-lime-50 hover:text-lime-900 border border-slate-200 transition flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-800">Kader:</span> kader1@tumbuh.id
                        </div>
                        <span class="text-slate-400 text-[10px]">Posyandu Melati</span>
                    </button>
                    <button type="button" 
                            onclick="document.getElementById('email').value='admin@tumbuh.id'; document.getElementById('password').value='password';"
                            class="w-full text-left p-2 rounded bg-slate-50 hover:bg-lime-50 hover:text-lime-900 border border-slate-200 transition flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-800">Admin:</span> admin@tumbuh.id
                        </div>
                        <span class="text-slate-400 text-[10px]">dr. Hendra Pratama</span>
                    </button>
                </div>
                <p class="text-[10px] text-slate-400 mt-2 text-center">Password default untuk semua akun: <code class="bg-slate-100 px-1 py-0.5 rounded">password</code></p>
            </div>
        </div>
    </div>
</div>
@endsection
