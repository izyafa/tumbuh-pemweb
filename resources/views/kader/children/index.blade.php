@extends('layouts.app')

@section('title', 'Data Balita & Registrasi KMS')
@section('page_title', 'Basis Data Balita Posyandu')

@section('content')
<div class="space-y-6">

    <!-- Header Actions & Filters -->
    <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-semibold text-slate-800">Daftar Rekam Medis Anak</h2>
                <p class="text-xs text-slate-500">Pencarian, pemantauan kurva pertumbuhan, dan akses token orang tua.</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('kader.measurements.batch') }}" 
                   class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-md border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium transition">
                    <i data-lucide="scale" class="w-3.5 h-3.5"></i>
                    <span>Input Batch</span>
                </a>
                <a href="{{ route('kader.children.create') }}" 
                   class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-md bg-lime-600 hover:bg-lime-700 text-white text-xs font-semibold shadow-sm transition">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Daftarkan Balita</span>
                </a>
            </div>
        </div>

        <!-- Search & Filter Form -->
        <form method="GET" action="{{ route('kader.children.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-3 border-t border-slate-100">
            <div class="sm:col-span-2">
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan Nama Balita, NIK, atau Nama Orang Tua..."
                           class="w-full pl-9 pr-3 py-1.5 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                </div>
            </div>

            <div>
                <select name="gender" onchange="this.form.submit()"
                        class="w-full py-1.5 px-2 text-xs rounded-md border border-slate-300 bg-white text-slate-700 focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                    <option value="">Semua Jenis Kelamin</option>
                    <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Laki-laki (L)</option>
                    <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Perempuan (P)</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <select name="status" onchange="this.form.submit()"
                        class="w-full py-1.5 px-2 text-xs rounded-md border border-slate-300 bg-white text-slate-700 focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                    <option value="">Semua Status Gizi</option>
                    <option value="severely_stunted" {{ request('status') === 'severely_stunted' ? 'selected' : '' }}>Sangat Pendek</option>
                    <option value="stunted" {{ request('status') === 'stunted' ? 'selected' : '' }}>Pendek (Stunted)</option>
                    <option value="normal" {{ request('status') === 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="tall" {{ request('status') === 'tall' ? 'selected' : '' }}>Tinggi</option>
                </select>
                @if(request()->hasAny(['search', 'gender', 'status']))
                    <a href="{{ route('kader.children.index') }}" class="p-1.5 text-slate-400 hover:text-rose-600 rounded" title="Reset filter">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Children Table -->
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">Nama Balita / NIK</th>
                        <th class="py-3 px-3">Gender / Usia</th>
                        <th class="py-3 px-3">Orang Tua / Kontak</th>
                        <th class="py-3 px-3 text-right">TB Terakhir</th>
                        <th class="py-3 px-3 text-right">BB Terakhir</th>
                        <th class="py-3 px-3 text-center">Status Stunting</th>
                        <th class="py-3 px-3 text-center">Portal Publik</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($children as $child)
                        @php $latest = $child->latestMeasurement; @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <!-- Name & NIK -->
                            <td class="py-3 px-4 font-medium text-slate-900">
                                <a href="{{ route('kader.children.show', $child->id) }}" class="font-semibold text-slate-900 hover:text-lime-700 hover:underline">
                                    {{ $child->full_name }}
                                </a>
                                <div class="text-[10px] text-slate-400 font-mono tracking-tight">{{ $child->nik }}</div>
                            </td>

                            <!-- Gender & Age -->
                            <td class="py-3 px-3 text-slate-600">
                                <div class="font-medium text-slate-800">{{ $child->gender_label }}</div>
                                <div class="text-[10px] text-slate-500 tabular-nums">
                                    {{ $latest ? $latest->age_in_months : $child->age_in_months }} Bulan
                                </div>
                            </td>

                            <!-- Parent Info -->
                            <td class="py-3 px-3 text-slate-600">
                                <div class="font-medium text-slate-800">{{ $child->parent_name }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $child->parent_phone }}</div>
                            </td>

                            <!-- Height -->
                            <td class="py-3 px-3 text-right tabular-nums text-slate-900 font-medium font-mono">
                                {{ $latest ? number_format($latest->height, 1) . ' cm' : '-' }}
                            </td>

                            <!-- Weight -->
                            <td class="py-3 px-3 text-right tabular-nums text-slate-900 font-medium font-mono">
                                {{ $latest ? number_format($latest->weight, 2) . ' kg' : '-' }}
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3 px-3 text-center">
                                @if($latest)
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium border {{ $latest->badge_class }}">
                                        {{ $latest->stunting_status_label }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[10px]">Belum diukur</span>
                                @endif
                            </td>

                            <!-- Public Portal Link -->
                            <td class="py-3 px-3 text-center">
                                <a href="{{ route('parent.portal', ['token' => $child->public_token]) }}" target="_blank"
                                   class="inline-flex items-center space-x-1 px-2 py-1 rounded text-[11px] font-medium border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 transition"
                                   title="Buka portal pemantauan orang tua">
                                    <i data-lucide="external-link" class="w-3 h-3 text-slate-500"></i>
                                    <span>Pantau</span>
                                </a>
                            </td>

                            <!-- Action -->
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center space-x-2">
                                    <a href="{{ route('kader.children.show', $child->id) }}" 
                                       class="px-2.5 py-1 rounded text-xs font-semibold bg-lime-50 text-lime-800 border border-lime-200 hover:bg-lime-100 transition">
                                        KMS Digital
                                    </a>
                                    <a href="{{ route('kader.children.edit', $child->id) }}" 
                                       class="p-1 text-slate-400 hover:text-slate-700 rounded transition" title="Edit Biodata">
                                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <i data-lucide="user-x" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                                <p class="text-sm">Tidak ditemukan data balita yang cocok.</p>
                                <a href="{{ route('kader.children.create') }}" class="mt-2 inline-block text-xs text-lime-700 font-semibold hover:underline">
                                    + Daftarkan balita baru sekarang
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($children->hasPages())
            <div class="px-5 py-3 border-t border-slate-200 bg-slate-50/50">
                {{ $children->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
