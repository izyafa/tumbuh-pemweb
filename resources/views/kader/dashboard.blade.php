@extends('layouts.app')

@section('title', 'Dashboard Kader Posyandu')
@section('page_title', 'Dashboard Operasional Posyandu')

@section('page_badge')
    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-lime-50 text-lime-800 border border-lime-200">
        {{ Auth::user()->posyandu_name ?? 'Posyandu Terpadu' }}
    </span>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-sm font-semibold text-slate-800">Hari Layanan Penimbangan Posyandu</h2>
            <p class="text-xs text-slate-500">Gunakan fitur input cepat untuk mencatat puluhan balita secara serentak.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('kader.measurements.batch') }}" 
               class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-md bg-lime-600 hover:bg-lime-700 text-white text-xs font-semibold shadow-sm transition">
                <i data-lucide="sparkles" class="w-4 h-4"></i>
                <span>Input Cepat Batch</span>
            </a>
            <a href="{{ route('kader.children.create') }}" 
               class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-md border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium transition">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Daftar Balita Baru</span>
            </a>
        </div>
    </div>

    <!-- Aggregate Metric Cards (High Data Density) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Total Children -->
        <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
            <div class="flex items-center justify-between text-slate-500 mb-1">
                <span class="text-xs font-medium uppercase tracking-wider">Total Balita</span>
                <i data-lucide="baby" class="w-4 h-4 text-slate-400"></i>
            </div>
            <div class="flex items-baseline space-x-2">
                <span class="text-2xl font-bold tracking-tight text-slate-900 tabular-nums">{{ $totalChildren }}</span>
                <span class="text-xs text-slate-500">anak terdata</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Wilayah binaan Posyandu</p>
        </div>

        <!-- Stunting Indicator -->
        <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
            <div class="flex items-center justify-between text-slate-500 mb-1">
                <span class="text-xs font-medium uppercase tracking-wider">Prevalensi Stunting</span>
                <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-500"></i>
            </div>
            <div class="flex items-baseline space-x-2">
                <span class="text-2xl font-bold tracking-tight text-rose-600 tabular-nums">{{ $stuntingRate }}%</span>
                <span class="text-xs font-medium text-slate-600 tabular-nums">({{ $stuntedCount }} anak)</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Z-Score TB/U &lt; -2 SD</p>
        </div>

        <!-- Weight Stagnation (2T) -->
        <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
            <div class="flex items-center justify-between text-slate-500 mb-1">
                <span class="text-xs font-medium uppercase tracking-wider">BB Tidak Naik (2T)</span>
                <i data-lucide="trending-down" class="w-4 h-4 text-amber-500"></i>
            </div>
            <div class="flex items-baseline space-x-2">
                <span class="text-2xl font-bold tracking-tight text-amber-600 tabular-nums">{{ $stagnantCount }}</span>
                <span class="text-xs text-slate-500">balita</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Risiko weight faltering</p>
        </div>

        <!-- Normal / Well-Nourished -->
        <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
            <div class="flex items-center justify-between text-slate-500 mb-1">
                <span class="text-xs font-medium uppercase tracking-wider">Tumbuh Normal</span>
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
            </div>
            <div class="flex items-baseline space-x-2">
                <span class="text-2xl font-bold tracking-tight text-emerald-700 tabular-nums">{{ $normalCount + $tallCount }}</span>
                <span class="text-xs text-slate-500">anak</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Mengikuti kurva hijau KMS</p>
        </div>
    </div>

    <!-- Triage Table: Children Needing Urgent Attention -->
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-700">
                    Balita Memerlukan Perhatian Khusus / Intervensi Klinis
                </h3>
            </div>
            <span class="text-xs text-slate-500">{{ $urgentChildren->count() }} kasus terdeteksi</span>
        </div>

        @if($urgentChildren->isEmpty())
            <div class="p-8 text-center text-slate-400 text-sm">
                <i data-lucide="smile" class="w-8 h-8 mx-auto text-emerald-500 mb-2"></i>
                <p class="font-medium text-slate-700">Kondisi Pertumbuhan Aman</p>
                <p class="text-xs mt-1">Tidak ada balita yang terdeteksi stunting akut atau mengalami stagnansi berat badan.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/75 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            <th class="py-2.5 px-4">Nama Balita</th>
                            <th class="py-2.5 px-3">Usia</th>
                            <th class="py-2.5 px-3">Gender</th>
                            <th class="py-2.5 px-3 text-right">TB (cm)</th>
                            <th class="py-2.5 px-3 text-right">BB (kg)</th>
                            <th class="py-2.5 px-3 text-center">Z-Score TB/U</th>
                            <th class="py-2.5 px-3">Status Stunting</th>
                            <th class="py-2.5 px-3">Tren BB</th>
                            <th class="py-2.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($urgentChildren as $ch)
                            @php $latest = $ch->latestMeasurement; @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-2.5 px-4 font-medium text-slate-900">
                                    <a href="{{ route('kader.children.show', $ch->id) }}" class="hover:text-lime-700 hover:underline">
                                        {{ $ch->full_name }}
                                    </a>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $ch->nik }}</div>
                                </td>
                                <td class="py-2.5 px-3 text-slate-600 tabular-nums">
                                    {{ $latest ? $latest->age_in_months . ' bln' : $ch->age_in_months . ' bln' }}
                                </td>
                                <td class="py-2.5 px-3 text-slate-600">
                                    {{ $ch->gender === 'male' ? 'L' : 'P' }}
                                </td>
                                <td class="py-2.5 px-3 text-right tabular-nums text-slate-900 font-medium">
                                    {{ $latest ? number_format($latest->height, 1) : '-' }}
                                </td>
                                <td class="py-2.5 px-3 text-right tabular-nums text-slate-900 font-medium">
                                    {{ $latest ? number_format($latest->weight, 1) : '-' }}
                                </td>
                                <td class="py-2.5 px-3 text-center tabular-nums font-mono">
                                    @if($latest)
                                        <span class="{{ $latest->zscore_tb_u < -3 ? 'text-rose-600 font-bold' : ($latest->zscore_tb_u < -2 ? 'text-amber-600 font-medium' : 'text-slate-700') }}">
                                            {{ $latest->zscore_tb_u > 0 ? '+' : '' }}{{ number_format($latest->zscore_tb_u, 2) }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-2.5 px-3">
                                    @if($latest)
                                        <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium border {{ $latest->badge_class }}">
                                            {{ $latest->stunting_status_label }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3">
                                    @if($latest && $latest->is_weight_stagnant)
                                        <span class="inline-flex items-center space-x-1 px-1.5 py-0.5 rounded text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                            <i data-lucide="arrow-down-right" class="w-3 h-3"></i>
                                            <span>2T (Stagnan)</span>
                                        </span>
                                    @else
                                        <span class="text-emerald-700 text-[11px]">Normal</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 text-right">
                                    <a href="{{ route('kader.children.show', $ch->id) }}" 
                                       class="inline-flex items-center space-x-1 px-2.5 py-1 rounded text-xs font-medium border border-slate-200 bg-white hover:bg-lime-50 hover:text-lime-800 transition">
                                        <span>Buka KMS</span>
                                        <i data-lucide="chevron-right" class="w-3 h-3"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Recent Measurement Activity Stream -->
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
        <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-700">Aktivitas Penimbangan Terakhir</h3>
            <a href="{{ route('kader.children.index') }}" class="text-xs text-lime-700 hover:underline font-medium">Lihat Semua Data &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/50 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-2.5 px-4">Tanggal</th>
                        <th class="py-2.5 px-3">Nama Balita</th>
                        <th class="py-2.5 px-3">Usia</th>
                        <th class="py-2.5 px-3 text-right">BB / TB</th>
                        <th class="py-2.5 px-3 text-center">Status TB/U</th>
                        <th class="py-2.5 px-3">Pencatat</th>
                        <th class="py-2.5 px-4 text-right">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentMeasurements as $rec)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-2.5 px-4 font-mono text-slate-500">
                                {{ $rec->measurement_date->format('d/m/Y') }}
                            </td>
                            <td class="py-2.5 px-3 font-medium text-slate-900">
                                <a href="{{ route('kader.children.show', $rec->child_id) }}" class="hover:text-lime-700 hover:underline">
                                    {{ $rec->child->full_name }}
                                </a>
                            </td>
                            <td class="py-2.5 px-3 text-slate-600 tabular-nums">
                                {{ $rec->age_in_months }} Bulan
                            </td>
                            <td class="py-2.5 px-3 text-right tabular-nums text-slate-800 font-medium font-mono">
                                {{ $rec->weight }} kg / {{ $rec->height }} cm
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium border {{ $rec->badge_class }}">
                                    {{ $rec->stunting_status_label }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-slate-500 text-[11px]">
                                {{ $rec->measuredBy->name ?? 'Kader' }}
                            </td>
                            <td class="py-2.5 px-4 text-right">
                                <a href="{{ route('kader.children.show', $rec->child_id) }}" class="text-lime-700 hover:text-lime-800 font-medium">
                                    KMS &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400">Belum ada riwayat penimbangan tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
