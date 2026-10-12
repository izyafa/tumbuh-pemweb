@extends('layouts.app')

@section('title', 'Analitik Sistem Administrator')
@section('page_title', 'Ringkasan Eksekutif & Analitik Agregat')

@section('page_badge')
    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-50 text-purple-800 border border-purple-200">
        Panel Administrator
    </span>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Top Summary Banner -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Pemantauan Intervensi Gizi & Stunting Komprehensif</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Data agregat seluruh Posyandu binaan
                </p>
            </div>
            <div class="flex items-center space-x-2 shrink-0">
                <a href="{{ route('admin.users') }}" 
                   class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-md border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium transition">
                    <i data-lucide="users" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>Kelola Kader</span>
                </a>
                <a href="{{ route('admin.videos') }}" 
                   class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-md bg-lime-600 hover:bg-lime-700 text-white text-xs font-semibold shadow-sm transition">
                    <i data-lucide="video" class="w-3.5 h-3.5"></i>
                    <span>Kelola Modul Video</span>
                </a>
            </div>
        </div>
    </div>

    <!-- System Metrics Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Children -->
        <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
            <span class="text-xs font-medium uppercase tracking-wider text-slate-400 block mb-1">Total Balita</span>
            <div class="flex items-baseline space-x-2">
                <span class="text-2xl font-bold tracking-tight text-slate-900 tabular-nums">{{ $totalChildren }}</span>
                <span class="text-xs text-slate-500">jiwa</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Tersebar di wilayah Posyandu</p>
        </div>

        <!-- Global Stunting Rate -->
        <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
            <span class="text-xs font-medium uppercase tracking-wider text-slate-400 block mb-1">Prevalensi Stunting</span>
            <div class="flex items-baseline space-x-2">
                <span class="text-2xl font-bold tracking-tight text-rose-600 tabular-nums">{{ $stuntingRate }}%</span>
                <span class="text-xs text-slate-500 tabular-nums">({{ $severelyStunted + $stunted }} anak)</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Target Nasional: &lt; 14%</p>
        </div>

        <!-- Total Kader Active -->
        <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
            <span class="text-xs font-medium uppercase tracking-wider text-slate-400 block mb-1">Kader Terdaftar</span>
            <div class="flex items-baseline space-x-2">
                <span class="text-2xl font-bold tracking-tight text-slate-900 tabular-nums">{{ $totalKaders }}</span>
                <span class="text-xs text-slate-500">petugas</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Pengguna lapangan aktif</p>
        </div>

        <!-- Total Measurement Records -->
        <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
            <span class="text-xs font-medium uppercase tracking-wider text-slate-400 block mb-1">Log Penimbangan</span>
            <div class="flex items-baseline space-x-2">
                <span class="text-2xl font-bold tracking-tight text-slate-900 tabular-nums">{{ $totalMeasurements }}</span>
                <span class="text-xs text-slate-500">kali timbang</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Riwayat biometrik tersimpan</p>
        </div>
    </div>

    <!-- Status Breakdown Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Stunting Distribution Breakdown -->
        <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Distribusi Status Pertumbuhan (TB/U WHO)</h3>
                <p class="text-[11px] text-slate-500">Hasil klasifikasi tinggi badan balita pada pengukuran terakhir.</p>
            </div>

            <div class="space-y-3 text-xs">
                <!-- Severely Stunted -->
                <div class="space-y-1">
                    <div class="flex justify-between font-medium">
                        <span class="text-rose-700">Sangat Pendek (Severely Stunted, &lt; -3 SD)</span>
                        <span class="font-mono font-bold">{{ $severelyStunted }} anak ({{ $totalChildren > 0 ? round(($severelyStunted / $totalChildren) * 100, 1) : 0 }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-rose-600 h-2 rounded-full" style="width: {{ $totalChildren > 0 ? ($severelyStunted / $totalChildren) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <!-- Stunted -->
                <div class="space-y-1">
                    <div class="flex justify-between font-medium">
                        <span class="text-amber-700">Pendek (Stunted, -3 s/d -2 SD)</span>
                        <span class="font-mono font-bold">{{ $stunted }} anak ({{ $totalChildren > 0 ? round(($stunted / $totalChildren) * 100, 1) : 0 }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $totalChildren > 0 ? ($stunted / $totalChildren) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <!-- Normal -->
                <div class="space-y-1">
                    <div class="flex justify-between font-medium">
                        <span class="text-emerald-700">Normal (-2 s/d +2 SD)</span>
                        <span class="font-mono font-bold">{{ $normal }} anak ({{ $totalChildren > 0 ? round(($normal / $totalChildren) * 100, 1) : 0 }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $totalChildren > 0 ? ($normal / $totalChildren) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <!-- Tall -->
                <div class="space-y-1">
                    <div class="flex justify-between font-medium">
                        <span class="text-slate-700">Tinggi (&gt; +2 SD)</span>
                        <span class="font-mono font-bold">{{ $tall }} anak ({{ $totalChildren > 0 ? round(($tall / $totalChildren) * 100, 1) : 0 }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-slate-400 h-2 rounded-full" style="width: {{ $totalChildren > 0 ? ($tall / $totalChildren) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weight Trend & Acute Risk Indicators -->
        <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Indikator Masalah Gizi Akut</h3>
                <p class="text-[11px] text-slate-500">Deteksi dini weight faltering dan wasting pada balita.</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 rounded-md bg-amber-50/75 border border-amber-200 space-y-1">
                    <span class="text-[10px] font-semibold uppercase tracking-wider text-amber-800">BB Tidak Naik (2T Rule)</span>
                    <div class="text-2xl font-bold font-mono text-amber-900">{{ $stagnantWeightCount }}</div>
                    <p class="text-[11px] text-amber-700 leading-snug">Balita dengan berat badan mendatar/turun 2 bulan berturut-turut.</p>
                </div>

                <div class="p-4 rounded-md bg-rose-50/75 border border-rose-200 space-y-1">
                    <span class="text-[10px] font-semibold uppercase tracking-wider text-rose-800">Wasting (Kurus / BB/TB)</span>
                    <div class="text-2xl font-bold font-mono text-rose-900">{{ $wastingCount }}</div>
                    <p class="text-[11px] text-rose-700 leading-snug">Gizi kurang atau gizi buruk akut berdasarkan rasio BB terhadap TB.</p>
                </div>
            </div>

            <div class="p-3 bg-slate-50 rounded-md border border-slate-200 text-xs text-slate-600 space-y-1">
                <span class="font-bold text-slate-800 block text-[11px]">Protokol Penanganan Puskesmas:</span>
                <p class="text-[11px]">
                    Balita dengan flag 2T (Stagnan) atau Z-score &lt; -2 SD wajib mendapatkan konseling gizi intensif, pemberian makanan tambahan (PMT) kaya protein hewani, dan penelusuran penyakit penyerta (TBC/ISPA/anemia).
                </p>
            </div>
        </div>
    </div>

    <!-- Recent Measurements Table -->
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
        <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-700">Entri Penimbangan Terkini di Seluruh Posyandu</h3>
            <a href="{{ route('kader.children.index') }}" class="text-xs text-lime-700 hover:underline font-medium">Buka Data Balita &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-2.5 px-4">Tanggal</th>
                        <th class="py-2.5 px-3">Nama Balita</th>
                        <th class="py-2.5 px-3 text-right">TB (cm)</th>
                        <th class="py-2.5 px-3 text-right">BB (kg)</th>
                        <th class="py-2.5 px-3 text-center">Status TB/U</th>
                        <th class="py-2.5 px-3">Petugas Pencatat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentRecords as $r)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-2.5 px-4 font-mono text-slate-500">{{ $r->measurement_date->format('d/m/Y') }}</td>
                            <td class="py-2.5 px-3 font-medium text-slate-900">
                                <a href="{{ route('kader.children.show', $r->child_id) }}" class="hover:underline hover:text-lime-700">
                                    {{ $r->child->full_name }}
                                </a>
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono">{{ $r->height }}</td>
                            <td class="py-2.5 px-3 text-right font-mono">{{ $r->weight }}</td>
                            <td class="py-2.5 px-3 text-center">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-medium border {{ $r->badge_class }}">
                                    {{ $r->stunting_status_label }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-slate-600 text-[11px]">{{ $r->measuredBy->name ?? 'Kader' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400">Belum ada riwayat pengukuran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
