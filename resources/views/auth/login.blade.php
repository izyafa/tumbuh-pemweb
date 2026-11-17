@extends('layouts.guest')

@section('title', 'Masuk Petugas & Kader')

@section('content')
<div class="relative w-full min-h-[calc(100vh-130px)] flex flex-col justify-center"
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

    <!-- Full-Bleed Two-Tone Background: Menempel langsung ke navbar & footer tanpa border luar -->
    <div class="absolute inset-0 flex flex-col pointer-events-none" aria-hidden="true">
        <!-- Top Half (Lime-700) Full-Width: Menempel langsung di bawah navbar -->
        <div class="h-[52%] min-h-[300px] bg-lime-700 relative overflow-hidden">
            <div class="absolute -right-20 -top-20 w-96 h-96 rounded-full border border-white/10 pointer-events-none"></div>
            <div class="absolute -right-36 -top-36 w-[32rem] h-[32rem] rounded-full border border-white/5 pointer-events-none"></div>
        </div>
        <!-- Bottom Half (Slate-50) Full-Width: Menempel ke footer -->
        <div class="flex-1 bg-slate-50"></div>
    </div>

    <!-- Foreground Content Layer: Terpusat secara proporsional di dalam container lebar -->
    <div class="relative z-10 w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 my-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            
            <!-- Sisi Kiri (lg:col-span-7): Brand Info di Atas & Akun Demo di Bawah -->
            <div class="lg:col-span-7 flex flex-col justify-between py-2">
                
                <!-- Kiri Atas: Menempel di area hijau penuh -->
                <div class="text-white">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-white p-1.5 shadow-md flex items-center justify-center shrink-0">
                            <img src="{{ asset('images/logo-tumbuh-clean.png') }}" 
                                 alt="Logo Tumbuh" 
                                 class="w-full h-full object-contain">
                        </div>
                        <div>
                            <span class="text-2xl font-black tracking-tight text-white block leading-none">TUMBUH</span>
                            <span class="text-xs font-semibold text-lime-200 tracking-wider uppercase block mt-1">Platform Posyandu Digital</span>
                        </div>
                    </div>

                    <div class="max-w-lg">
                        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white leading-tight">
                            Masuk ke Portal Tumbuh
                        </h1>
                        <p class="mt-3 text-base text-lime-50/90 leading-relaxed">
                            Sistem terintegrasi pencatatan balita, otomatisasi kurva KMS digital, dan kalkulasi Z-Score WHO 2006 untuk kader Posyandu.
                        </p>
                    </div>
                </div>

                <!-- Kiri Bawah: Menempel di area terang penuh (Login As Profiles) -->
                <div class="mt-10 lg:mt-16 pt-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-3">
                        Masuk sebagai (Akun Demo):
                    </span>

                    <div class="flex flex-wrap gap-3">
                        <!-- Profile Card 1: Kader Posyandu -->
                        <button type="button"
                                @click="selectAccount('kader', 'kader1@tumbuh.id', 'password')"
                                :class="selectedRole === 'kader' ? 'border-lime-600 ring-2 ring-lime-600/30 bg-white shadow-md' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/80 shadow-2xs'"
                                class="w-40 p-3.5 rounded-2xl border text-center transition duration-150 cursor-pointer group">
                            <div class="w-12 h-12 rounded-full bg-lime-100 text-lime-800 font-bold flex items-center justify-center text-sm mx-auto mb-2 border border-lime-200 group-hover:scale-105 transition">
                                KP
                            </div>
                            <span class="font-bold text-xs text-slate-900 block truncate">Ibu Siti Rahma</span>
                            <span class="text-[11px] text-slate-500 block truncate mt-0.5">Kader Posyandu</span>
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-lime-100 text-lime-700 mt-1.5">
                                Aktif
                            </span>
                        </button>

                        <!-- Profile Card 2: Admin Puskesmas -->
                        <button type="button"
                                @click="selectAccount('admin', 'admin@tumbuh.id', 'password')"
                                :class="selectedRole === 'admin' ? 'border-lime-600 ring-2 ring-lime-600/30 bg-white shadow-md' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/80 shadow-2xs'"
                                class="w-40 p-3.5 rounded-2xl border text-center transition duration-150 cursor-pointer group">
                            <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-sm mx-auto mb-2 border border-emerald-200 group-hover:scale-105 transition">
                                DR
                            </div>
                            <span class="font-bold text-xs text-slate-900 block truncate">dr. Hendra</span>
                            <span class="text-[11px] text-slate-500 block truncate mt-0.5">Admin Puskesmas</span>
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700 mt-1.5">
                                Aktif
                            </span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- Sisi Kanan (lg:col-span-5): SATU-SATUNYA KOTAK MENGAMBANG (THE FLOATING FORM CARD) -->
            <div class="lg:col-span-5 flex justify-center lg:justify-end">
                <div class="w-full max-w-md bg-white rounded-3xl border border-slate-200/90 shadow-2xl shadow-slate-900/15 p-7 sm:p-9 relative">
                    
                    <!-- Header Form -->
                    <div class="mb-6">
                        <span class="text-xs font-bold text-lime-700 uppercase tracking-wider block mb-1">
                            Selamat Datang di TUMBUH
                        </span>
                        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">
                            Masuk
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">
                            Masukkan email dan kata sandi akun petugas.
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

                    <!-- Form Login -->
                    <form action="{{ route('login') }}" 
                          method="POST" 
                          @submit="submitting = true" 
                          class="space-y-4">
                        @csrf

                        <!-- Field Email -->
                        <div>
                            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Alamat Email
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   required 
                                   autocomplete="email" 
                                   autofocus
                                   value="{{ old('email', 'kader1@tumbuh.id') }}"
                                   placeholder="kader1@tumbuh.id"
                                   class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-300 bg-slate-50/50 text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-lime-600 focus:ring-2 focus:ring-lime-600/20 transition duration-150">
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
                                <label for="password" class="block text-xs font-semibold text-slate-700">
                                    Kata Sandi
                                </label>
                                <span class="text-[11px] text-slate-400">
                                    Default: <code class="bg-slate-100 text-slate-700 px-1 py-0.5 rounded text-[10px] font-mono">password</code>
                                </span>
                            </div>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" 
                                       name="password" 
                                       id="password" 
                                       required 
                                       autocomplete="current-password"
                                       value="password"
                                       placeholder="Masukkan kata sandi"
                                       class="w-full px-3.5 pr-10 py-2.5 text-sm rounded-xl border border-slate-300 bg-slate-50/50 text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-lime-600 focus:ring-2 focus:ring-lime-600/20 transition duration-150">
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
                                    class="w-full py-3 px-4 rounded-xl bg-lime-600 hover:bg-lime-700 active:bg-lime-800 text-white font-bold text-sm transition duration-150 shadow-md shadow-lime-600/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-600 flex items-center justify-center space-x-2 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
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
                                        <span>Memverifikasi...</span>
                                    </span>
                                </template>
                            </button>
                        </div>
                    </form>

                    <!-- Trust Note -->
                    <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                        <p class="text-[11px] text-slate-400 flex items-center justify-center space-x-1">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5 text-lime-600"></i>
                            <span>Standar Baku Antropometri Kemenkes & WHO</span>
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
