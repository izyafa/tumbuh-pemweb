@extends('layouts.app')

@section('title', 'Laporan Bulanan Posyandu (SKDN / SIP)')
@section('page_title', 'Laporan Bulanan & Ekspor Rekapitulasi')

@section('page_badge')
    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-lime-50 text-lime-800 border border-lime-200">
        {{ $posyanduName }}
    </span>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Top Action & Month Filter Bar -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Rekapitulasi Kinerja SKDN & Deteksi Stunting</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Laporan berkala untuk Bidan Desa, Puskesmas Pembina, dan Dinas Kesehatan.
                </p>
            </div>

            <!-- Month Selector & Export Buttons -->
            <div class="flex flex-wrap items-center gap-2.5">
                <form method="GET" action="{{ route('kader.reports.index') }}" class="flex items-center space-x-2">
                    <label for="month" class="text-xs font-semibold uppercase tracking-wider text-slate-600">Bulan:</label>
                    <input type="month" name="month" id="month" value="{{ $selectedMonth }}" onchange="this.form.submit()"
                           class="px-3 py-1.5 rounded-md border border-slate-300 text-xs font-mono text-slate-800 bg-white focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                </form>

                <a href="{{ route('kader.reports.export-csv', ['month' => $selectedMonth]) }}"
                   class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition">
                    <i data-lucide="sheet" class="w-4 h-4"></i>
                    <span>Unduh Excel (.CSV)</span>
                </a>

                <a href="{{ route('kader.reports.print', ['month' => $selectedMonth]) }}" target="_blank"
                   class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-md border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition">
                    <i data-lucide="printer" class="w-4 h-4 text-slate-500"></i>
                    <span>Cetak / Cetak PDF</span>
                </a>
            </div>
        </div>
    </div>

    <!-- SKDN Key Performance Indicators (Grid 4) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Sasaran (S) -->
        <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Sasaran Balita (S)</span>
            <div class="flex items-baseline space-x-2">
                <span class="text-2xl font-bold text-slate-900 tabular-nums">{{ $totalChildren }}</span>
                <span class="text-xs text-slate-500">anak terdaftar</span>
            </div>
            <p class="text-[10px] text-slate-400 mt-1.5">Memiliki KMS: {{ $totalWithKms }} (100%)</p>
        </div>

        <!-- Ditimbang (D) & Cakupan D/S -->
        <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Balita Ditimbang (D)</span>
            <div class="flex items-baseline space-x-2">
                <span class="text-2xl font-bold text-lime-700 tabular-nums">{{ $totalMeasured }}</span>
                <span class="text-xs font-semibold text-lime-800">D/S: {{ $dsCoverage }}%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                <div class="bg-lime-600 h-1.5 rounded-full" style="width: {{ min(100, $dsCoverage) }}%"></div>
            </div>
        </div>

        <!-- Naik BB (N) & Keberhasilan N/D -->
        <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Naik Berat Badan (N)</span>
            <div class="flex items-baseline space-x-2">
                <span class="text-2xl font-bold text-emerald-700 tabular-nums">{{ $totalWeightGain }}</span>
                <span class="text-xs font-semibold text-emerald-800">N/D: {{ $ndSuccess }}%</span>
            </div>
            <p class="text-[10px] text-amber-600 mt-1.5 font-medium">BB Stagnan (2T): {{ $totalStagnant }} anak</p>
        </div>

        <!-- Stunting Rate -->
        <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Prevalensi Stunting</span>
            <div class="flex items-baseline space-x-2">
                <span class="text-2xl font-bold text-rose-600 tabular-nums">{{ $stuntingRate }}%</span>
                <span class="text-xs text-slate-600">({{ $stuntedCount }} kasus)</span>
            </div>
            <p class="text-[10px] text-rose-700 mt-1.5 font-medium">Sangat Pendek: {{ $severelyStuntedCount }} anak</p>
        </div>
    </div>

    <!-- Monthly Detailed Measurement Table -->
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center space-x-2">
                <i data-lucide="clipboard-list" class="w-4 h-4 text-slate-600"></i>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">
                    Daftar Hasil Penimbangan Periode {{ $monthCarbon->translatedFormat('F Y') }}
                </h3>
            </div>
            <span class="text-xs text-slate-500 font-mono">{{ $records->count() }} catatan</span>
        </div>

        @if($records->isEmpty())
            <div class="p-10 text-center text-slate-400 text-sm">
                <i data-lucide="calendar-x" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                <p class="font-medium text-slate-700">Belum Ada Data Penimbangan pada Bulan Ini</p>
                <p class="text-xs mt-1">Pilih bulan sebelumnya atau lakukan input penimbangan batch untuk bulan ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            <th class="py-2.5 px-3 text-center">#</th>
                            <th class="py-2.5 px-4">Nama Balita / NIK</th>
                            <th class="py-2.5 px-3">Tgl Timbang</th>
                            <th class="py-2.5 px-3">Usia</th>
                            <th class="py-2.5 px-3 text-right">TB (cm)</th>
                            <th class="py-2.5 px-3 text-right">BB (kg)</th>
                            <th class="py-2.5 px-3 text-center">Z-Score TB/U</th>
                            <th class="py-2.5 px-3">Status Stunting</th>
                            <th class="py-2.5 px-3">Status 2T</th>
                            <th class="py-2.5 px-4">Nama Orang Tua</th>
                            <th class="py-2.5 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($records as $index => $rec)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]">{{ $index + 1 }}</td>
                                <td class="py-2.5 px-4 font-medium text-slate-900">
                                    <a href="{{ route('kader.children.show', $rec->child_id) }}" class="hover:text-lime-700 hover:underline">
                                        {{ $rec->child->full_name }}
                                    </a>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $rec->child->nik }}</div>
                                </td>
                                <td class="py-2.5 px-3 text-slate-600 font-mono">
                                    {{ $rec->measurement_date->format('d/m/Y') }}
                                </td>
                                <td class="py-2.5 px-3 text-slate-600 tabular-nums">
                                    {{ $rec->age_in_months }} bln ({{ $rec->child->gender === 'male' ? 'L' : 'P' }})
                                </td>
                                <td class="py-2.5 px-3 text-right tabular-nums font-mono font-medium text-slate-900">
                                    {{ number_format($rec->height, 1) }}
                                </td>
                                <td class="py-2.5 px-3 text-right tabular-nums font-mono font-medium text-slate-900">
                                    {{ number_format($rec->weight, 2) }}
                                </td>
                                <td class="py-2.5 px-3 text-center tabular-nums font-mono">
                                    <span class="{{ $rec->zscore_tb_u < -3 ? 'text-rose-600 font-bold' : ($rec->zscore_tb_u < -2 ? 'text-amber-600 font-medium' : 'text-slate-700') }}">
                                        {{ $rec->zscore_tb_u > 0 ? '+' : '' }}{{ number_format($rec->zscore_tb_u, 2) }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-medium border {{ $rec->badge_class }}">
                                        {{ $rec->stunting_status_label }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3">
                                    @if($rec->is_weight_stagnant)
                                        <span class="inline-flex items-center space-x-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span>2T (Stagnan)</span>
                                        </span>
                                    @else
                                        <span class="text-emerald-700 text-[11px] font-medium">N (Naik)</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 text-slate-600">
                                    {{ $rec->child->parent_name }}
                                    <div class="text-[10px] text-slate-400">{{ $rec->child->parent_phone }}</div>
                                </td>
                                <td class="py-2.5 px-3 text-right">
                                    <a href="{{ route('kader.children.show', $rec->child_id) }}" 
                                       class="text-lime-700 hover:text-lime-900 font-medium text-xs">
                                        KMS &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
