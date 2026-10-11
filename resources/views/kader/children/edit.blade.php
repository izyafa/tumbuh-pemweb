@extends('layouts.app')

@section('title', 'Edit Balita — ' . $child->full_name)
@section('page_title', 'Perbarui Biodata Balita')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('kader.children.show', $child->id) }}" class="inline-flex items-center space-x-1 text-xs text-slate-500 hover:text-slate-800 transition">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Kembali ke Profil Balita</span>
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-6">
        <div class="border-b border-slate-100 pb-4 mb-5">
            <h2 class="text-base font-semibold text-slate-900">Perbarui Identitas Balita</h2>
            <p class="text-xs text-slate-500 mt-0.5">Perubahan data kelahiran akan mempengaruhi kalkulasi Z-Score historis balita.</p>
        </div>

        <form action="{{ route('kader.children.update', $child->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- NIK & Full Name -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nik" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Nomor Induk Kependudukan (NIK) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nik" id="nik" maxlength="16" required
                           value="{{ old('nik', $child->nik) }}" placeholder="Contoh: 3201014501230001"
                           class="w-full px-3 py-2 text-xs font-mono rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                    @error('nik') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="full_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Nama Lengkap Balita <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="full_name" id="full_name" required
                           value="{{ old('full_name', $child->full_name) }}" placeholder="Contoh: Muhammad Kenzo Al-Fatih"
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
                           value="{{ old('date_of_birth', $child->date_of_birth->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                    @error('date_of_birth') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="gender" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Jenis Kelamin <span class="text-rose-500">*</span>
                    </label>
                    <select name="gender" id="gender" required
                            class="w-full px-3 py-2 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                        <option value="male" {{ old('gender', $child->gender) === 'male' ? 'selected' : '' }}>Laki-laki (L)</option>
                        <option value="female" {{ old('gender', $child->gender) === 'female' ? 'selected' : '' }}>Perempuan (P)</option>
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
                           value="{{ old('birth_weight', $child->birth_weight) }}" placeholder="Contoh: 3.25"
                           class="w-full px-3 py-2 text-xs font-mono rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                    @error('birth_weight') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="birth_height" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Panjang Badan Lahir (cm) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="0.1" min="20" max="70" name="birth_height" id="birth_height" required
                           value="{{ old('birth_height', $child->birth_height) }}" placeholder="Contoh: 49.5"
                           class="w-full px-3 py-2 text-xs font-mono rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
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
                           value="{{ old('parent_name', $child->parent_name) }}" placeholder="Contoh: Ibu Rina & Bpk Ahmad"
                           class="w-full px-3 py-2 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                    @error('parent_name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="parent_phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Nomor WhatsApp Orang Tua <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="parent_phone" id="parent_phone" required
                           value="{{ old('parent_phone', $child->parent_phone) }}" placeholder="Contoh: 081234567890"
                           class="w-full px-3 py-2 text-xs font-mono rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                    @error('parent_phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Address -->
            <div>
                <label for="address" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                    Alamat Domisili <span class="text-rose-500">*</span>
                </label>
                <textarea name="address" id="address" rows="2" required placeholder="Contoh: Jl. Mawar No. 12, RT 02/RW 03, Kel. Sukamaju"
                          class="w-full px-3 py-2 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">{{ old('address', $child->address) }}</textarea>
                @error('address') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <button type="button" 
                        onclick="confirmDelete(document.getElementById('delete-child-form'), {
                            title: 'Hapus Data Balita',
                            message: 'Apakah Anda yakin ingin menghapus data balita ini? Seluruh data riwayat penimbangan, imunisasi, dan kurva KMS akan dihapus permanen.',
                            itemDetail: '{{ addslashes($child->full_name) }} (NIK: {{ $child->nik }})',
                            confirmText: 'Ya, Hapus Balita'
                        })"
                        class="text-xs text-rose-600 hover:text-rose-800 font-semibold transition cursor-pointer">
                    Hapus Data Balita
                </button>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('kader.children.show', $child->id) }}" class="px-4 py-2 rounded-md text-xs font-medium text-slate-600 hover:text-slate-900 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 rounded-md bg-lime-600 hover:bg-lime-700 text-white text-xs font-semibold shadow-sm transition flex items-center space-x-1.5 cursor-pointer">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </div>
        </form>

        <!-- Separate Delete Form (Fixed nested form issue) -->
        <form id="delete-child-form" action="{{ route('kader.children.destroy', $child->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>

</div>
@endsection
