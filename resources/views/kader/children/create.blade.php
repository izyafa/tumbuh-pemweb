@extends('layouts.app')

@section('title', 'Daftarkan Balita Baru')
@section('page_title', 'Registrasi Balita Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('kader.children.index') }}" class="inline-flex items-center space-x-1 text-xs text-slate-500 hover:text-slate-800 transition">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Kembali ke Daftar Balita</span>
        </a>
    </div>

    <!-- Registration Card -->
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6">
        <div class="border-b border-slate-100 pb-4 mb-5">
            <h2 class="text-base font-semibold text-slate-900">Formulir Identitas & Riwayat Kelahiran</h2>
            <p class="text-xs text-slate-500 mt-0.5">Lengkapi identitas kependudukan balita untuk inisialisasi Kartu Menuju Sehat (KMS) digital.</p>
        </div>

        <form action="{{ route('kader.children.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- NIK & Full Name -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nik" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Nomor Induk Kependudukan (NIK) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nik" id="nik" maxlength="16" required
                           value="{{ old('nik') }}" placeholder="Contoh: 3201014501230001"
                           class="w-full px-3 py-2 text-xs font-mono rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                    @error('nik') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="full_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Nama Lengkap Balita <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="full_name" id="full_name" required
                           value="{{ old('full_name') }}" placeholder="Contoh: Muhammad Kenzo Al-Fatih"
                           class="w-full px-3 py-2 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                    @error('full_name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Date of Birth & Gender -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="date_of_birth" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Tanggal Lahir <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="date_of_birth" id="date_of_birth" required max="{{ date('Y-m-d') }}"
                           value="{{ old('date_of_birth') }}"
                           class="w-full px-3 py-2 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                    @error('date_of_birth') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="gender" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Jenis Kelamin <span class="text-rose-500">*</span>
                    </label>
                    <select name="gender" id="gender" required
                            class="w-full px-3 py-2 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Laki-laki (L)</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Perempuan (P)</option>
                    </select>
                    @error('gender') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Birth Metrics -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-md bg-slate-50 border border-slate-200">
                <div>
                    <label for="birth_weight" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Berat Lahir (kg) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="0.01" min="0.5" max="10" name="birth_weight" id="birth_weight" required
                           value="{{ old('birth_weight', '3.1') }}" placeholder="Contoh: 3.2"
                           class="w-full px-3 py-2 text-xs font-mono rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                    <p class="text-[10px] text-slate-400 mt-1">BBLR jika &lt; 2.5 kg</p>
                    @error('birth_weight') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="birth_height" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Panjang Badan Lahir (cm) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="0.1" min="20" max="70" name="birth_height" id="birth_height" required
                           value="{{ old('birth_height', '49.0') }}" placeholder="Contoh: 49.5"
                           class="w-full px-3 py-2 text-xs font-mono rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                    <p class="text-[10px] text-slate-400 mt-1">Rata-rata bayi cukup bulan: 48–52 cm</p>
                    @error('birth_height') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Parent Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="parent_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Nama Orang Tua / Wali <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="parent_name" id="parent_name" required
                           value="{{ old('parent_name') }}" placeholder="Contoh: Ibu Rina & Bpk Ahmad"
                           class="w-full px-3 py-2 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                    @error('parent_name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="parent_phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Nomor WhatsApp Orang Tua <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="parent_phone" id="parent_phone" required
                           value="{{ old('parent_phone') }}" placeholder="Contoh: 081234567890"
                           class="w-full px-3 py-2 text-xs font-mono rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                    <p class="text-[10px] text-slate-400 mt-1">Untuk pengiriman ringkasan KMS via WhatsApp</p>
                    @error('parent_phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Address -->
            <div>
                <label for="address" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                    Alamat Domisili <span class="text-rose-500">*</span>
                </label>
                <textarea name="address" id="address" rows="2" required placeholder="Contoh: Jl. Mawar No. 12, RT 02/RW 03, Kel. Sukamaju"
                          class="w-full px-3 py-2 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">{{ old('address') }}</textarea>
                @error('address') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('kader.children.index') }}" class="px-4 py-2 rounded-md text-xs font-medium text-slate-600 hover:text-slate-900 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 rounded-md bg-lime-600 hover:bg-lime-700 text-white text-xs font-semibold shadow-sm transition flex items-center space-x-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Daftarkan Balita</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
