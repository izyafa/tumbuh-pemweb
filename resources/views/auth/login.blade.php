@extends('layouts.guest')

@section('title', 'Masuk Petugas & Kader')

@section('content')
<div class="py-6 sm:py-10 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto"
     x-data="{
         showPassword: false,
         selectedRole: 'kader',
         submitting: false,
         selectAccount(role, email, pass) {
             this.selectedRole = role;
             const emailInput = document.getElementById('email');
             const passInput = document.getElementById('password');
             if (emailInput && passInput) {
                 emailInput.value = email;
                 passInput.value = pass;
                 emailInput.focus();
             }
         }
     }">

    <!-- Main Container: Overlapping Split Canvas (Desain 3 Architecture) -->
    <div class="rounded-3xl border border-slate-200/90 bg-white shadow-xl shadow-slate-900/5 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-12">
            
            <!-- Sisi Kiri: Brand Hero & Quick Role Switcher (Desain 3 Left Zone) -->
            <div class="lg:col-span-7 bg-slate-900 text-white p-6 sm:p-10 lg:p-12 flex flex-col justify-between relative overflow-hidden">
                <!-- Background Subtle Ambient Glow (Non-slop, clean dark surface) -->
                <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full bg-lime-500/10 blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -right-24 w-80 h-80 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>

                <div class="relative z-10">
                    <!-- Brand Badge & Logo -->
                    <div class="flex items-center space-x-3 mb-8">
                        <img src="{{ asset('images/logo-tumbuh-clean.png') }}" 
                             alt="Logo Tumbuh" 
                             class="w-11 h-11 object-contain drop-shadow-sm">
                        <div>
                            <span class="text-xl font-bold tracking-tight text-white block leading-none">TUMBUH</span>
                            <span class="text-[11px] font-medium text-lime-400 tracking-wider uppercase block mt-1">Platform Posyandu Digital</span>
                        </div>
                    </div>

                    <!-- Headline & Deskripsi Sistem -->
                    <div class="max-w-xl">
                        <div class="inline-flex items-center space-x-2 px-2.5 py-1 rounded-full bg-lime-500/10 border border-lime-500/20 text-lime-300 text-xs font-medium mb-4">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                            <span>Standar Antropometri Kemenkes & WHO 2006</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white leading-tight">
                            Portal Pencatatan Pertumbuhan Balita & Deteksi Dini Stunting
                        </h1>
                        <p class="mt-3 text-sm text-slate-300 leading-relaxed">
                            Otomatisasi kalkulasi Z-Score antropometri, kurva KMS digital interaktif, dan panduan nutrisi klinis berbasis AI untuk kader Posyandu dan tenaga kesehatan komunitas.
                        </p>
                    </div>

                    <!-- Key Feature Highlights -->
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-slate-300">
                        <div class="flex items-center space-x-2.5 p-2.5 rounded-xl bg-white/5 border border-white/10">
                            <div class="w-7 h-7 rounded-lg bg-lime-500/20 text-lime-400 flex items-center justify-center shrink-0">
                                <i data-lucide="calculator" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <span class="font-semibold text-white block">Kalkulasi Otomatis</span>
                                <span class="text-[11px] text-slate-400">Z-Score BB/U, TB/U, BB/TB</span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2.5 p-2.5 rounded-xl bg-white/5 border border-white/10">
                            <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                                <i data-lucide="line-chart" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <span class="font-semibold text-white block">KMS Digital Interaktif</span>
                                <span class="text-[11px] text-slate-400">Grafik pertumbuhan real-time</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bagian Kiri Bawah: "Login As" Quick Profile Switcher (Desain 3 Unique Feature) -->
                <div class="relative z-10 mt-8 pt-6 border-t border-slate-800">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold text-slate-300 uppercase tracking-wider flex items-center space-x-1.5">
                            <i data-lucide="users" class="w-3.5 h-3.5 text-lime-400"></i>
                            <span>Masuk Cepat Sebagai (Akun Demo):</span>
                        </span>
                        <span class="text-[10px] text-slate-400">Klik untuk isi otomatis</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Profil Kader -->
                        <button type="button"
                                @click="selectAccount('kader', 'kader1@tumbuh.id', 'password')"
                                :class="selectedRole === 'kader' ? 'ring-2 ring-lime-400 border-lime-400/50 bg-white/10' : 'border-white/10 bg-white/5 hover:bg-white/10'"
                                class="text-left p-3 rounded-2xl border transition duration-150 flex items-center space-x-3 cursor-pointer group">
                            <div class="w-10 h-10 rounded-xl bg-lime-600 text-white font-bold flex items-center justify-center text-sm shadow-xs shrink-0">
                                KP
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-xs text-white truncate group-hover:text-lime-300 transition">Kader Posyandu</span>
                                    <span class="w-2 h-2 rounded-full bg-lime-400" title="Aktif"></span>
                                </div>
                                <p class="text-[11px] text-slate-300 font-medium">Ibu Siti Rahma, S.Pd</p>
                                <p class="text-[10px] text-slate-400 font-mono truncate">kader1@tumbuh.id</p>
                            </div>
                        </button>

                        <!-- Profil Admin Puskesmas -->
                        <button type="button"
                                @click="selectAccount('admin', 'admin@tumbuh.id', 'password')"
                                :class="selectedRole === 'admin' ? 'ring-2 ring-lime-400 border-lime-400/50 bg-white/10' : 'border-white/10 bg-white/5 hover:bg-white/10'"
                                class="text-left p-3 rounded-2xl border transition duration-150 flex items-center space-x-3 cursor-pointer group">
                            <div class="w-10 h-10 rounded-xl bg-emerald-700 text-white font-bold flex items-center justify-center text-sm shadow-xs shrink-0">
                                DR
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-xs text-white truncate group-hover:text-lime-300 transition">Admin Puskesmas</span>
                                    <span class="w-2 h-2 rounded-full bg-emerald-400" title="Aktif"></span>
                                </div>
                                <p class="text-[11px] text-slate-300 font-medium">dr. Hendra Pratama</p>
                                <p class="text-[10px] text-slate-400 font-mono truncate">admin@tumbuh.id</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sisi Kanan: The Floating Form Card (Desain 3 Right Card) -->
            <div class="lg:col-span-5 bg-white p-6 sm:p-10 lg:p-12 flex flex-col justify-between">
                <div>
                    <!-- Header Form -->
                    <div class="mb-6">
                        <span class="text-xs font-semibold uppercase tracking-wider text-lime-700 block mb-1">
                            Akses Masuk Petugas
                        </span>
                        <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                            Masuk ke Akun
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">
                            Silakan masukkan kredensial akun Anda untuk mengakses sistem.
                        </p>
                    </div>

                    <!-- Session Status Alerts -->
                    @if(session('error'))
                        <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start space-x-2.5">
                            <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0 mt-0.5"></i>
                            <span class="leading-relaxed">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-start space-x-2.5">
                            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5"></i>
                            <span class="leading-relaxed">{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form action="{{ route('login') }}" 
                          method="POST" 
                          @submit="submitting = true" 
                          class="space-y-4">
                        @csrf

                        <!-- Field Email -->
                        <div>
                            <label for="email" class="block text-xs font-medium text-slate-700 mb-1.5">
                                Alamat Email
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="mail" class="w-4 h-4"></i>
                                </div>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       required 
                                       autocomplete="email" 
                                       autofocus
                                       value="{{ old('email', 'kader1@tumbuh.id') }}"
                                       placeholder="kader1@tumbuh.id"
                                       class="w-full pl-9 pr-3 py-2.5 text-sm rounded-xl border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-lime-600 focus:ring-2 focus:ring-lime-600/20 transition duration-150">
                            </div>
                            @error('email')
                                <p class="mt-1.5 text-xs text-rose-600 flex items-center space-x-1">
                                    <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <!-- Field Password -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="password" class="block text-xs font-medium text-slate-700">
                                    Kata Sandi
                                </label>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    Default: <code class="bg-slate-100 text-slate-700 px-1 py-0.5 rounded text-[10px]">password</code>
                                </span>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="lock" class="w-4 h-4"></i>
                                </div>
                                <input :type="showPassword ? 'text' : 'password'" 
                                       name="password" 
                                       id="password" 
                                       required 
                                       autocomplete="current-password"
                                       value="password"
                                       placeholder="Masukkan kata sandi"
                                       class="w-full pl-9 pr-10 py-2.5 text-sm rounded-xl border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-lime-600 focus:ring-2 focus:ring-lime-600/20 transition duration-150">
                                <button type="button" 
                                        @click="showPassword = !showPassword" 
                                        aria-label="Tampilkan atau sembunyikan kata sandi"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition focus:outline-none cursor-pointer">
                                    <i data-lucide="eye" class="w-4 h-4" x-show="!showPassword"></i>
                                    <i data-lucide="eye-off" class="w-4 h-4" x-show="showPassword" x-cloak></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1.5 text-xs text-rose-600 flex items-center space-x-1">
                                    <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" 
                                    :disabled="submitting"
                                    class="w-full py-3 px-4 rounded-xl bg-lime-600 hover:bg-lime-700 active:bg-lime-800 text-white font-medium text-sm transition duration-150 shadow-xs focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-600 flex items-center justify-center space-x-2 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                                <template x-if="!submitting">
                                    <span class="inline-flex items-center space-x-2">
                                        <i data-lucide="log-in" class="w-4 h-4"></i>
                                        <span>Masuk ke Dashboard</span>
                                    </span>
                                </template>
                                <template x-if="submitting">
                                    <span class="inline-flex items-center space-x-2">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span>Memverifikasi Akun...</span>
                                    </span>
                                </template>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Footer Micro-Security Trust Signal -->
                <div class="mt-8 pt-4 border-t border-slate-100 text-center">
                    <p class="text-[11px] text-slate-400 flex items-center justify-center space-x-1">
                        <i data-lucide="lock" class="w-3 h-3 text-slate-400"></i>
                        <span>Enkripsi Sesi Terlindungi • Hanya Petugas Berwenang</span>
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
