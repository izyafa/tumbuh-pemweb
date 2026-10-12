@extends('layouts.app')

@section('title', 'KMS & Profil Balita — ' . $child->full_name)
@section('page_title', 'Kartu Menuju Sehat (KMS) & Rekam Gizi')

@section('page_badge')
    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $child->gender === 'male' ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-pink-50 text-pink-700 border border-pink-200' }}">
        {{ $child->gender_label }} • NIK: {{ $child->nik }}
    </span>
@endsection

@section('content')
<div class="space-y-6" x-data="kmsPage()">

    <!-- Profile Header & Clinical Metadata Card -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            
            <!-- Left: Identity -->
            <div class="space-y-1">
                <div class="flex items-center space-x-2">
                    <h2 class="text-xl font-bold tracking-tight text-slate-900">{{ $child->full_name }}</h2>
                    <a href="{{ route('kader.children.edit', $child->id) }}" class="text-slate-400 hover:text-slate-700" title="Edit Profil">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                    </a>
                </div>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
                    <span>Lahir: <strong class="text-slate-700">{{ $child->date_of_birth->translatedFormat('d F Y') }}</strong></span>
                    <span>Usia Saat Ini: <strong class="text-slate-700">{{ $child->age_in_months }} Bulan</strong></span>
                    <span>BBLR: <strong class="text-slate-700 font-mono">{{ $child->birth_weight }} kg</strong></span>
                    <span>PBL: <strong class="text-slate-700 font-mono">{{ $child->birth_height }} cm</strong></span>
                </div>
                <div class="text-xs text-slate-500">
                    <span>Orang Tua: <strong class="text-slate-700">{{ $child->parent_name }}</strong> ({{ $child->parent_phone }})</span>
                    <span class="mx-1">•</span>
                    <span>Alamat: {{ $child->address }}</span>
                </div>
            </div>

            <!-- Right: Latest Clinical Status & Action Buttons -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 shrink-0">
                @if($latestMeasurement)
                    <div class="p-2.5 rounded-md border {{ $latestMeasurement->badge_class }} text-left">
                        <div class="text-[10px] font-semibold uppercase tracking-wider opacity-75">Status Pertumbuhan Terakhir</div>
                        <div class="text-xs font-bold">{{ $latestMeasurement->stunting_status_label }}</div>
                        <div class="text-[11px] font-mono mt-0.5">
                            TB/U: {{ $latestMeasurement->zscore_tb_u > 0 ? '+' : '' }}{{ $latestMeasurement->zscore_tb_u }} SD
                            @if($latestMeasurement->is_weight_stagnant)
                                • <span class="font-bold underline">BB Stagnan (2T)</span>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="flex flex-wrap items-center gap-2">
                    <!-- Print KMS -->
                    <a href="{{ route('kader.children.kms-print', $child->id) }}" target="_blank"
                       class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-md border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition">
                        <i data-lucide="printer" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span>Cetak KMS</span>
                    </a>

                    <!-- Rekomendasi Menu Gizi AI -->
                    <a href="{{ route('kader.children.ai-consult.show', $child->id) }}"
                       class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-md border border-lime-300 bg-lime-50 hover:bg-lime-100 text-lime-800 text-xs font-semibold transition">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5 text-lime-600"></i>
                        <span>Menu Gizi AI</span>
                    </a>

                    <!-- Add Immunization Trigger -->
                    <button type="button" @click="showImunisasiModal = true; setTab('imunisasi');"
                            class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-md border border-sky-300 bg-sky-50 hover:bg-sky-100 text-sky-800 text-xs font-semibold transition cursor-pointer">
                        <i data-lucide="syringe" class="w-3.5 h-3.5 text-sky-600"></i>
                        <span>Catat Imunisasi</span>
                    </button>

                    <!-- WhatsApp Share Modal Trigger -->
                    <button type="button" @click="showWaModal = true"
                            class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-md border border-emerald-300 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-semibold transition">
                        <i data-lucide="message-circle-more" class="w-3.5 h-3.5 text-emerald-700"></i>
                        <span>WhatsApp Ibu</span>
                    </button>

                    <!-- Add Single Measurement Trigger -->
                    <button type="button" @click="showAddModal = true"
                            class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-md bg-lime-600 hover:bg-lime-700 text-white text-xs font-semibold shadow-sm transition">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Catat Pengukuran</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Digital KMS Growth Curve Card -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Kurva Pertumbuhan Digital KMS (Standar WHO 0–60 Bulan)</h3>
                <p class="text-xs text-slate-500">Garis referensi deviasi standar (SD) resmi WHO dengan plotting titik penimbangan balita.</p>
            </div>
            <!-- Curve Type Toggle -->
            <div class="inline-flex rounded-md p-1 bg-slate-100 border border-slate-200 text-xs font-medium">
                <button type="button" 
                        @click="switchChart('tb_u')" 
                        :class="chartType === 'tb_u' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-1 rounded transition">
                    Tinggi Badan (TB/U)
                </button>
                <button type="button" 
                        @click="switchChart('bb_u')" 
                        :class="chartType === 'bb_u' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-1 rounded transition">
                    Berat Badan (BB/U)
                </button>
            </div>
        </div>

        <!-- Legend indicators -->
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[11px] text-slate-600 bg-slate-50 p-2.5 rounded-md border border-slate-200">
            <span class="font-semibold text-slate-700">Keterangan Garis:</span>
            <div class="flex items-center space-x-1.5">
                <span class="w-3 h-0.5 bg-emerald-600 inline-block"></span>
                <span>Median 0 SD (Ideal)</span>
            </div>
            <div class="flex items-center space-x-1.5">
                <span class="w-3 h-0.5 bg-amber-600 inline-block"></span>
                <span>-2 SD (Batas Waspada Stunting)</span>
            </div>
            <div class="flex items-center space-x-1.5">
                <span class="w-3 h-0.5 bg-rose-600 inline-block"></span>
                <span>-3 SD (Batas Bahaya Severely Stunted)</span>
            </div>
            <div class="flex items-center space-x-1.5">
                <span class="w-3 h-0.5 bg-slate-400 inline-block border-t border-dashed"></span>
                <span>+2 SD (Batas Atas Normal)</span>
            </div>
            <div class="flex items-center space-x-1.5 font-bold text-slate-900">
                <span class="w-2.5 h-2.5 rounded-full bg-lime-600 border border-slate-900 inline-block"></span>
                <span>Titik Pengukuran Balita</span>
            </div>
        </div>

        <!-- Chart Canvas Container -->
        <div class="relative w-full h-[360px] sm:h-[420px]">
            <canvas id="kmsChartCanvas"></canvas>
        </div>
    </div>

    <!-- AI Clinical Nutrition Advisor Banner (Separated to dedicated page) -->
    <div class="bg-gradient-to-r from-lime-50/80 via-white to-emerald-50/80 border border-lime-200 rounded-lg p-4 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-lg bg-lime-600 text-white flex items-center justify-center shrink-0 shadow-2xs">
                <i data-lucide="sparkles" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">Rekomendasi Menu Makan &amp; Gizi Balita (AI)</h3>
                    <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-lime-100 text-lime-800 font-semibold border border-lime-200">Gemini AI</span>
                </div>
                <p class="text-xs text-slate-600 mt-0.5">
                    @if($child->aiConsultations->isNotEmpty())
                        Tersedia riwayat menu gizi (terakhir {{ $child->aiConsultations->first()->created_at->translatedFormat('d M Y, H:i') }}). Racik menu harian sesuai kondisi antropometri balita.
                    @else
                        Konsultasi gizi klinis Kemenkes RI & penyusunan variasi menu pangan lokal terjangkau sesuai anggaran keluarga balita.
                    @endif
                </p>
            </div>
        </div>
        <a href="{{ route('kader.children.ai-consult.show', $child->id) }}"
           class="inline-flex items-center justify-center space-x-1.5 px-4 py-2 rounded-md bg-lime-600 hover:bg-lime-700 text-white text-xs font-bold transition shrink-0 shadow-2xs">
            <i data-lucide="utensils" class="w-4 h-4"></i>
            <span>Buka Rekomendasi Menu AI</span>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        </a>
    </div>

    <!-- Table Tabs Navigation -->
    <div class="border-b border-slate-200">
        <nav class="flex space-x-2 sm:space-x-4">
            <button type="button" @click="setTab('penimbangan')"
                    :class="activeTab === 'penimbangan' 
                        ? 'border-lime-600 text-lime-800 bg-lime-50/70 border-b-2 font-bold' 
                        : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-medium'"
                    class="py-2.5 px-3.5 sm:px-4 rounded-t-lg transition-colors flex items-center space-x-2 text-xs sm:text-sm cursor-pointer">
                <i data-lucide="scale" class="w-4 h-4"></i>
                <span>Riwayat Penimbangan &amp; Pengukuran</span>
                <span class="px-2 py-0.5 text-[10px] font-mono rounded-full bg-slate-100 text-slate-700 font-semibold border border-slate-200">
                    {{ $child->measurementRecords->count() }}
                </span>
            </button>
            <button type="button" @click="setTab('imunisasi')"
                    :class="activeTab === 'imunisasi' 
                        ? 'border-sky-600 text-sky-800 bg-sky-50/70 border-b-2 font-bold' 
                        : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-medium'"
                    class="py-2.5 px-3.5 sm:px-4 rounded-t-lg transition-colors flex items-center space-x-2 text-xs sm:text-sm cursor-pointer">
                <i data-lucide="syringe" class="w-4 h-4"></i>
                <span>Buku Imunisasi &amp; Vitamin A</span>
                <span class="px-2 py-0.5 text-[10px] font-mono rounded-full bg-sky-100 text-sky-800 font-semibold border border-sky-200">
                    {{ $child->immunizationRecords->count() }}
                </span>
            </button>
        </nav>
    </div>

    <!-- Tab 1: Riwayat Penimbangan & Pengukuran -->
    <div x-show="activeTab === 'penimbangan'" class="space-y-4">
        <!-- Measurement Records Historical Table -->
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
            <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-700">Riwayat Penimbangan &amp; Pengukuran Lengkap</h3>
                    <p class="text-[11px] text-slate-500">Semua entri log biometrik yang tercatat di KMS digital.</p>
                </div>
                <button type="button" @click="showAddModal = true"
                        class="text-xs text-lime-700 hover:text-lime-800 font-semibold">
                    + Tambah Pengukuran
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            <th class="py-2.5 px-4">Tanggal Pengukuran</th>
                            <th class="py-2.5 px-3">Usia</th>
                            <th class="py-2.5 px-3 text-right">TB / PB (cm)</th>
                            <th class="py-2.5 px-3 text-right">BB (kg)</th>
                            <th class="py-2.5 px-3 text-right">LK / LiLA</th>
                            <th class="py-2.5 px-3 text-center">Z-Score TB/U</th>
                            <th class="py-2.5 px-3 text-center">Z-Score BB/U</th>
                            <th class="py-2.5 px-3 text-center">Z-Score BB/TB</th>
                            <th class="py-2.5 px-3">Status Stunting</th>
                            <th class="py-2.5 px-3">Tren BB</th>
                            <th class="py-2.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($child->measurementRecords as $rec)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2.5 px-4 font-mono font-medium text-slate-900">
                                    {{ $rec->measurement_date->format('d/m/Y') }}
                                </td>
                                <td class="py-2.5 px-3 text-slate-700 tabular-nums">
                                    {{ $rec->age_in_months }} bln
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono font-medium text-slate-900 tabular-nums">
                                    {{ number_format($rec->height, 1) }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono font-medium text-slate-900 tabular-nums">
                                    {{ number_format($rec->weight, 2) }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono text-slate-500 tabular-nums text-[11px]">
                                    {{ $rec->head_circum ? $rec->head_circum . ' cm' : '-' }} / {{ $rec->arm_circum ? $rec->arm_circum . ' cm' : '-' }}
                                </td>
                                <td class="py-2.5 px-3 text-center font-mono tabular-nums font-semibold {{ $rec->zscore_tb_u < -2 ? 'text-rose-600' : 'text-slate-800' }}">
                                    {{ $rec->zscore_tb_u > 0 ? '+' : '' }}{{ number_format($rec->zscore_tb_u, 2) }}
                                </td>
                                <td class="py-2.5 px-3 text-center font-mono tabular-nums text-slate-700">
                                    {{ $rec->zscore_bb_u > 0 ? '+' : '' }}{{ number_format($rec->zscore_bb_u, 2) }}
                                </td>
                                <td class="py-2.5 px-3 text-center font-mono tabular-nums text-slate-700">
                                    {{ $rec->zscore_bb_tb > 0 ? '+' : '' }}{{ number_format($rec->zscore_bb_tb, 2) }}
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-medium border {{ $rec->badge_class }}">
                                        {{ $rec->stunting_status_label }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3">
                                    @if($rec->is_weight_stagnant)
                                        <span class="inline-flex items-center space-x-1 px-1.5 py-0.5 rounded text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                            <span>2T Stagnan</span>
                                        </span>
                                    @else
                                        <span class="text-emerald-700 text-[10px]">Naik</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 text-right">
                                    <form action="{{ route('kader.measurements.destroy', $rec->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" 
                                                onclick="confirmDelete(this, {
                                                    title: 'Hapus Catatan Penimbangan',
                                                    message: 'Apakah Anda yakin ingin menghapus data penimbangan tanggal {{ $rec->measurement_date->format('d/m/Y') }}? Grafik pertumbuhan KMS akan diperbarui secara otomatis.',
                                                    itemDetail: 'TB: {{ $rec->height }} cm • BB: {{ $rec->weight }} kg ({{ $rec->stunting_status_label }})',
                                                    confirmText: 'Ya, Hapus Data'
                                                })"
                                                class="text-slate-400 hover:text-rose-600 p-1 transition cursor-pointer" title="Hapus">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-6 text-center text-slate-400">Belum ada riwayat penimbangan tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab 2: Buku Imunisasi & Vitamin A -->
    <div x-show="activeTab === 'imunisasi'" class="space-y-4" x-cloak>
        <!-- Buku Imunisasi Dasar & Suplementasi Vitamin A Card -->
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                <div>
                    <div class="flex items-center space-x-2">
                        <i data-lucide="shield-plus" class="w-4 h-4 text-sky-600"></i>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Buku Imunisasi Dasar Lengkap &amp; Suplementasi</h3>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-0.5">Pencegahan infeksi berulang dan penurunan risiko stunting melalui imunisasi rutin Kemenkes RI.</p>
                </div>
                <button type="button" @click="showImunisasiModal = true"
                        class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-md bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-2xs transition">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Catat Vaksin</span>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            <th class="py-2.5 px-3 text-center w-10">#</th>
                            <th class="py-2.5 px-4">Nama Vaksin / Suplemen</th>
                            <th class="py-2.5 px-3">Kategori</th>
                            <th class="py-2.5 px-3">Tgl Diberikan</th>
                            <th class="py-2.5 px-3">No. Batch</th>
                            <th class="py-2.5 px-3">Dicatat Oleh</th>
                            <th class="py-2.5 px-3">Catatan</th>
                            <th class="py-2.5 px-4 text-right w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($child->immunizationRecords as $idx => $im)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]">{{ $idx + 1 }}</td>
                                <td class="py-2.5 px-4 font-bold text-slate-900">{{ $im->vaccine_name }}</td>
                                <td class="py-2.5 px-3">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold border
                                        {{ $im->category === 'vitamin_a' ? 'bg-amber-50 text-amber-800 border-amber-200' : ($im->category === 'obat_cacing' ? 'bg-purple-50 text-purple-800 border-purple-200' : 'bg-sky-50 text-sky-800 border-sky-200') }}">
                                        {{ $im->category_label }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 font-mono text-slate-700">{{ $im->administered_date->translatedFormat('d F Y') }}</td>
                                <td class="py-2.5 px-3 font-mono text-slate-500">{{ $im->batch_number ?? '-' }}</td>
                                <td class="py-2.5 px-3 text-slate-600">{{ $im->recordedBy->name ?? 'Kader Posyandu' }}</td>
                                <td class="py-2.5 px-3 text-slate-500 text-[11px]">{{ $im->notes ?? '-' }}</td>
                                <td class="py-2.5 px-4 text-right">
                                    <form action="{{ route('kader.immunizations.destroy', $im->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" 
                                                onclick="confirmDelete(this, {
                                                    title: 'Hapus Catatan Imunisasi / Suplemen',
                                                    message: 'Apakah Anda yakin ingin menghapus catatan pemberian {{ addslashes($im->vaccine_name) }} ini dari buku KMS balita?',
                                                    itemDetail: '{{ addslashes($im->vaccine_name) }} • Diberikan: {{ $im->administered_date->translatedFormat('d F Y') }}',
                                                    confirmText: 'Ya, Hapus Data'
                                                })"
                                                class="text-slate-400 hover:text-rose-600 p-1 transition cursor-pointer" title="Hapus">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-6 text-center text-slate-400">Belum ada catatan imunisasi atau vitamin A yang tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal: Catat Imunisasi & Vitamin A -->
    <div x-show="showImunisasiModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg border border-slate-200 shadow-xl max-w-lg w-full p-6 space-y-4" @click.away="showImunisasiModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2 text-sky-700">
                    <i data-lucide="syringe" class="w-5 h-5"></i>
                    <h3 class="text-sm font-bold text-slate-900">Catat Imunisasi & Vitamin A Balita</h3>
                </div>
                <button type="button" @click="showImunisasiModal = false" class="text-slate-400 hover:text-slate-700">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form action="{{ route('kader.children.immunizations.store', $child->id) }}" method="POST" class="space-y-4 text-xs" x-data="{ vacCategory: 'imunisasi_dasar', vacName: '' }">
                @csrf

                <!-- Kategori -->
                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">Kategori Pelayanan</label>
                    <select name="category" x-model="vacCategory" required
                            class="w-full px-3 py-2 rounded-md border border-slate-300 bg-white text-slate-800 text-xs focus:outline-none focus:border-sky-600 focus:ring-1 focus:ring-sky-600">
                        <option value="imunisasi_dasar">Imunisasi Dasar / Lanjutan</option>
                        <option value="vitamin_a">Suplementasi Vitamin A (Bulan Vit A)</option>
                        <option value="obat_cacing">Pemberian Obat Cacing (Albendazole)</option>
                    </select>
                </div>

                <!-- Nama Vaksin & Quick Presets -->
                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">Nama Vaksin / Suplemen</label>
                    <input type="text" name="vaccine_name" x-model="vacName" required
                           class="w-full px-3 py-2 rounded-md border border-slate-300 bg-white text-slate-800 text-xs focus:outline-none focus:border-sky-600 focus:ring-1 focus:ring-sky-600"
                           placeholder="Contoh: DPT-HB-Hib 1, Campak-Rubela, Vitamin A Biru">

                    <!-- Quick buttons -->
                    <div class="flex flex-wrap gap-1 mt-2">
                        <template x-if="vacCategory === 'imunisasi_dasar'">
                            <div class="flex flex-wrap gap-1">
                                <button type="button" @click="vacName = 'HB0 (Hepatitis B)'" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-sky-100 text-[10px] text-slate-600">HB0</button>
                                <button type="button" @click="vacName = 'BCG'" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-sky-100 text-[10px] text-slate-600">BCG</button>
                                <button type="button" @click="vacName = 'Polio 1'" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-sky-100 text-[10px] text-slate-600">Polio 1</button>
                                <button type="button" @click="vacName = 'DPT-HB-Hib 1'" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-sky-100 text-[10px] text-slate-600">DPT 1</button>
                                <button type="button" @click="vacName = 'PCV 1'" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-sky-100 text-[10px] text-slate-600">PCV 1</button>
                                <button type="button" @click="vacName = 'IPV (Polio Suntik)'" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-sky-100 text-[10px] text-slate-600">IPV</button>
                                <button type="button" @click="vacName = 'Campak-Rubela (MR)'" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-sky-100 text-[10px] text-slate-600">Campak MR</button>
                            </div>
                        </template>
                        <template x-if="vacCategory === 'vitamin_a'">
                            <div class="flex flex-wrap gap-1">
                                <button type="button" @click="vacName = 'Vitamin A Biru (100.000 IU, 6-11 Bln)'" class="px-2 py-0.5 rounded bg-amber-50 hover:bg-amber-100 text-[10px] text-amber-800">Vit A Biru (6-11 bln)</button>
                                <button type="button" @click="vacName = 'Vitamin A Merah (200.000 IU, 12-59 Bln)'" class="px-2 py-0.5 rounded bg-amber-50 hover:bg-amber-100 text-[10px] text-amber-800">Vit A Merah (12-59 bln)</button>
                            </div>
                        </template>
                        <template x-if="vacCategory === 'obat_cacing'">
                            <div class="flex flex-wrap gap-1">
                                <button type="button" @click="vacName = 'Obat Cacing Albendazole 400mg'" class="px-2 py-0.5 rounded bg-purple-50 hover:bg-purple-100 text-[10px] text-purple-800">Albendazole</button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Tanggal Diberikan -->
                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">Tanggal Pemberian</label>
                    <input type="date" name="administered_date" required max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 rounded-md border border-slate-300 bg-white text-slate-800 text-xs focus:outline-none focus:border-sky-600 focus:ring-1 focus:ring-sky-600">
                </div>

                <!-- No Batch -->
                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">Nomor Batch Vaksin (Opsional)</label>
                    <input type="text" name="batch_number" placeholder="Contoh: BIO-4921"
                           class="w-full px-3 py-2 rounded-md border border-slate-300 bg-white text-slate-800 text-xs focus:outline-none focus:border-sky-600 focus:ring-1 focus:ring-sky-600">
                </div>

                <!-- Notes -->
                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                    <textarea name="notes" rows="2" placeholder="Kondisi anak saat disuntik, reaksi obat, dsb."
                              class="w-full px-3 py-2 rounded-md border border-slate-300 bg-white text-slate-800 text-xs focus:outline-none focus:border-sky-600 focus:ring-1 focus:ring-sky-600"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="showImunisasiModal = false"
                            class="px-3.5 py-2 rounded-md border border-slate-300 bg-white text-slate-700 font-medium hover:bg-slate-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-md bg-sky-600 text-white font-semibold hover:bg-sky-700 shadow-sm transition cursor-pointer">
                        Simpan Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Catat Pengukuran Single Entry -->
    <div x-show="showAddModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg border border-slate-200 shadow-xl max-w-lg w-full p-6 space-y-4" @click.away="showAddModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">Catat Penimbangan & Pengukuran Balita</h3>
                <button type="button" @click="showAddModal = false" class="text-slate-400 hover:text-slate-700">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form action="{{ route('kader.measurements.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <input type="hidden" name="child_id" value="{{ $child->id }}">

                <div>
                    <label for="modal_date" class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">Tanggal Pengukuran</label>
                    <input type="date" name="measurement_date" id="modal_date" required max="{{ date('Y-m-d') }}"
                           value="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-1.5 rounded border border-slate-300 bg-white text-slate-800">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="modal_weight" class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">Berat Badan (kg) *</label>
                        <input type="number" step="0.01" min="1" max="50" name="weight" id="modal_weight" required placeholder="Contoh: 9.8"
                               class="w-full px-3 py-1.5 font-mono rounded border border-slate-300 bg-white text-slate-800">
                    </div>
                    <div>
                        <label for="modal_height" class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">Tinggi/Panjang (cm) *</label>
                        <input type="number" step="0.1" min="30" max="150" name="height" id="modal_height" required placeholder="Contoh: 76.5"
                               class="w-full px-3 py-1.5 font-mono rounded border border-slate-300 bg-white text-slate-800">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="modal_head" class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">Lingkar Kepala (cm)</label>
                        <input type="number" step="0.1" min="20" max="70" name="head_circum" id="modal_head" placeholder="Contoh: 44.5 (Opsional)"
                               class="w-full px-3 py-1.5 font-mono rounded border border-slate-300 bg-white text-slate-800">
                    </div>
                    <div>
                        <label for="modal_arm" class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">LiLA / Lingkar Lengan (cm)</label>
                        <input type="number" step="0.1" min="5" max="35" name="arm_circum" id="modal_arm" placeholder="Contoh: 14.0 (Opsional)"
                               class="w-full px-3 py-1.5 font-mono rounded border border-slate-300 bg-white text-slate-800">
                    </div>
                </div>

                <div>
                    <label for="modal_notes" class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">Catatan Tambahan</label>
                    <textarea name="notes" id="modal_notes" rows="2" placeholder="Nafsu makan, riwayat penyakit minggu lalu..."
                              class="w-full px-3 py-1.5 rounded border border-slate-300 bg-white text-slate-800"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="showAddModal = false" class="px-3 py-1.5 rounded text-slate-600 hover:text-slate-900">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-1.5 rounded bg-lime-600 hover:bg-lime-700 text-white font-semibold">
                        Simpan Pengukuran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: WhatsApp Share Link Generator -->
    <div x-show="showWaModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg border border-slate-200 shadow-xl max-w-lg w-full p-6 space-y-4" @click.away="showWaModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2 text-emerald-700">
                    <i data-lucide="message-circle-more" class="w-5 h-5"></i>
                    <h3 class="text-sm font-bold text-slate-900">Kirim Ringkasan KMS ke WhatsApp Ibu</h3>
                </div>
                <button type="button" @click="showWaModal = false" class="text-slate-400 hover:text-slate-700">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <div class="space-y-3 text-xs">
                <p class="text-slate-600">Pesan otomatis berikut siap dikirimkan langsung ke nomor WhatsApp orang tua (<strong>{{ $child->parent_phone }}</strong>):</p>
                
                <textarea readonly rows="8" class="w-full p-3 font-mono text-[11px] rounded bg-slate-50 border border-slate-200 text-slate-800 select-all leading-relaxed">{{ $waText }}</textarea>

                <div class="flex items-center justify-between pt-2" x-data="{ copied: false }">
                    <button type="button" 
                            @click="navigator.clipboard.writeText(`{{ addslashes($waText) }}`); copied = true; setTimeout(() => copied = false, 2500);"
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium cursor-pointer transition">
                        <template x-if="!copied">
                            <span class="inline-flex items-center space-x-1">
                                <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                <span>Salin Teks</span>
                            </span>
                        </template>
                        <template x-if="copied">
                            <span class="inline-flex items-center space-x-1 text-emerald-700 font-bold">
                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                <span>Teks Berhasil Disalin!</span>
                            </span>
                        </template>
                    </button>

                    <a href="{{ $waShareUrl }}" target="_blank"
                       class="inline-flex items-center space-x-2 px-4 py-2 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition shadow-sm">
                        <i data-lucide="message-circle-more" class="w-3.5 h-3.5"></i>
                        <span>Buka WhatsApp Sekarang</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function kmsPage() {
        return {
            showAddModal: false,
            showWaModal: false,
@php
    $defaultTab = (session('success') && (str_contains(session('success'), 'Vaksin') || str_contains(session('success'), 'Pencatatan') || str_contains(session('success'), 'Imunisasi') || str_contains(session('success'), 'imunisasi'))) ? 'imunisasi' : 'penimbangan';
@endphp
            activeTab: window.location.hash === '#imunisasi' ? 'imunisasi' : '{{ $defaultTab }}',
            chartType: 'tb_u',
            chartInstance: null,

            tbCurves: @json($tbuCurves),
            bbCurves: @json($bbuCurves),
            childHeightData: @json($childHeightPoints),
            childWeightData: @json($childWeightPoints),

            init() {
                if (window.location.hash === '#imunisasi') {
                    this.activeTab = 'imunisasi';
                }
                this.renderChart();
                if (window.lucide && window.lucide.createIcons) {
                    window.lucide.createIcons();
                }
            },

            setTab(tab) {
                this.activeTab = tab;
                this.$nextTick(() => {
                    if (window.lucide && window.lucide.createIcons) {
                        window.lucide.createIcons();
                    }
                });
            },

            switchChart(type) {
                this.chartType = type;
                this.renderChart();
            },

            renderChart() {
                const ctx = document.getElementById('kmsChartCanvas');
                if (!ctx) return;

                if (this.chartInstance) {
                    this.chartInstance.destroy();
                }

                const isTb = this.chartType === 'tb_u';
                const ref = isTb ? this.tbCurves : this.bbCurves;
                const childData = isTb ? this.childHeightData : this.childWeightData;
                const unit = isTb ? 'cm' : 'kg';
                const yLabel = isTb ? 'Panjang / Tinggi Badan (cm)' : 'Berat Badan (kg)';

                this.chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ref.labels,
                        datasets: [
                            // +2 SD
                            {
                                label: '+2 SD',
                                data: ref.plus2SD,
                                borderColor: '#94a3b8',
                                borderWidth: 1.5,
                                borderDash: [4, 4],
                                pointRadius: 0,
                                fill: false,
                                tension: 0.3
                            },
                            // Median 0 SD
                            {
                                label: 'Median (0 SD)',
                                data: ref.median,
                                borderColor: '#16A34A',
                                borderWidth: 2,
                                pointRadius: 0,
                                fill: false,
                                tension: 0.3
                            },
                            // -2 SD (Stunting threshold)
                            {
                                label: '-2 SD (Waspada)',
                                data: ref.minus2SD,
                                borderColor: '#D97706',
                                borderWidth: 1.5,
                                borderDash: [4, 4],
                                pointRadius: 0,
                                fill: false,
                                tension: 0.3
                            },
                            // -3 SD (Severely Stunted)
                            {
                                label: '-3 SD (Bahaya)',
                                data: ref.minus3SD,
                                borderColor: '#DC2626',
                                borderWidth: 1.5,
                                borderDash: [4, 4],
                                pointRadius: 0,
                                fill: false,
                                tension: 0.3
                            },
                            // Child Data Plot
                            {
                                label: 'Pertumbuhan {{ $child->full_name }}',
                                data: childData,
                                borderColor: '#0F172A',
                                borderWidth: 2.5,
                                pointBackgroundColor: '#65A30D',
                                pointBorderColor: '#0F172A',
                                pointBorderWidth: 2,
                                pointRadius: 4.5,
                                pointHoverRadius: 6.5,
                                spanGaps: true,
                                fill: false,
                                tension: 0.2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    title: (items) => 'Usia: ' + items[0].label + ' Bulan',
                                    label: (item) => item.dataset.label + ': ' + (item.raw !== null ? item.raw + ' ' + unit : '-')
                                }
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Usia (Bulan)',
                                    font: { size: 11, family: 'Roboto' }
                                },
                                grid: {
                                    color: '#f1f5f9'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: yLabel,
                                    font: { size: 11, family: 'Roboto' }
                                },
                                grid: {
                                    color: '#f1f5f9'
                                }
                            }
                        }
                    }
                });
            }
        }
    }
</script>
@endpush
@endsection
