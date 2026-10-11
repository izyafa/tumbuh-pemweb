@extends('layouts.guest')

@section('title', 'Pemantauan Pertumbuhan — ' . $child->full_name)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 space-y-6" x-data="parentKms()">

    <!-- Top Mother-Friendly Header -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-lime-50 text-lime-800 border border-lime-200">
                    <i data-lucide="shield-check" class="w-3 h-3 text-lime-600"></i>
                    <span>Portal Resmi Pemantauan Posyandu</span>
                </span>
                <h1 class="text-xl font-bold tracking-tight text-slate-900">{{ $child->full_name }}</h1>
                <p class="text-xs text-slate-500">
                    Lahir: {{ $child->date_of_birth->translatedFormat('d F Y') }} • Usia: <strong>{{ $child->age_in_months }} Bulan</strong> • {{ $child->gender_label }}
                </p>
            </div>

            <!-- Actions: Print KMS & WhatsApp -->
            <div class="shrink-0 flex flex-wrap items-center gap-2">
                <a href="{{ route('parent.kms-print', $child->public_token) }}" target="_blank"
                   class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 rounded-md border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition shadow-2xs">
                    <i data-lucide="printer" class="w-4 h-4 text-slate-500"></i>
                    <span>Cetak Kartu KMS</span>
                </a>
                <a href="{{ $waShareUrl }}" target="_blank"
                   class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-sm">
                    <i data-lucide="message-circle-more" class="w-4 h-4"></i>
                    <span>Bagikan ke WhatsApp Keluarga</span>
                </a>
            </div>
        </div>

        <!-- 60-Month Milestone Progress Bar -->
        <div class="mt-5 pt-4 border-t border-slate-100">
            <div class="flex items-center justify-between text-xs text-slate-600 mb-1.5 font-medium">
                <span>Perjalanan 1.000 Hari Pertama Kehidupan (HPK)</span>
                <span class="tabular-nums font-mono font-bold text-lime-800">{{ $child->age_in_months }} / 60 Bulan ({{ round(min(100, ($child->age_in_months / 60) * 100)) }}%)</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                <div class="bg-lime-600 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, ($child->age_in_months / 60) * 100) }}%"></div>
            </div>
        </div>
    </div>

    <!-- Current Biometric Status Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <!-- Height -->
        <div class="bg-white border border-slate-200 rounded-lg p-3.5 shadow-sm text-center">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Tinggi Badan</span>
            <span class="text-xl font-bold text-slate-900 tabular-nums font-mono">
                {{ $latestMeasurement ? number_format($latestMeasurement->height, 1) : '-' }} <span class="text-xs font-normal text-slate-500">cm</span>
            </span>
            <span class="text-[11px] text-slate-500 block mt-1">
                Lahir: {{ $child->birth_height }} cm
            </span>
        </div>

        <!-- Weight -->
        <div class="bg-white border border-slate-200 rounded-lg p-3.5 shadow-sm text-center">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Berat Badan</span>
            <span class="text-xl font-bold text-slate-900 tabular-nums font-mono">
                {{ $latestMeasurement ? number_format($latestMeasurement->weight, 2) : '-' }} <span class="text-xs font-normal text-slate-500">kg</span>
            </span>
            <span class="text-[11px] text-slate-500 block mt-1">
                Lahir: {{ $child->birth_weight }} kg
            </span>
        </div>

        <!-- Status Growth -->
        <div class="bg-white border border-slate-200 rounded-lg p-3.5 shadow-sm text-center col-span-2 sm:col-span-2">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Status Gizi & Stunting</span>
            @if($latestMeasurement)
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold border {{ $latestMeasurement->badge_class }}">
                    {{ $latestMeasurement->stunting_status_label }}
                </span>
                <span class="text-[11px] text-slate-500 block mt-1">
                    {{ $latestMeasurement->is_weight_stagnant ? '⚠️ Perhatian: Kenaikan BB Stagnan (2T)' : '✅ Kenaikan BB Normal' }}
                </span>
            @else
                <span class="text-xs text-slate-400">Belum ada pengukuran</span>
            @endif
        </div>
    </div>

    <!-- Digital KMS Growth Chart (Read-Only) -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Grafik Digital KMS Balita</h2>
                <p class="text-xs text-slate-500">Standar Baku Antropometri Anak WHO 2006 (Kemenkes RI)</p>
            </div>
            <!-- Toggle -->
            <div class="inline-flex rounded-md p-1 bg-slate-100 border border-slate-200 text-xs font-medium">
                <button type="button" @click="switchChart('tb_u')"
                        :class="chartType === 'tb_u' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600'"
                        class="px-3 py-1 rounded transition">
                    Tinggi Badan (TB/U)
                </button>
                <button type="button" @click="switchChart('bb_u')"
                        :class="chartType === 'bb_u' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600'"
                        class="px-3 py-1 rounded transition">
                    Berat Badan (BB/U)
                </button>
            </div>
        </div>

        <div class="relative w-full h-[320px] sm:h-[380px]">
            <canvas id="parentKmsCanvas"></canvas>
        </div>

        <div class="p-3 bg-slate-50 rounded-md border border-slate-200 text-[11px] text-slate-600 flex items-center justify-between flex-wrap gap-2">
            <span><strong>Cara Membaca KMS:</strong> Titik pertumbuhan balita (lingkaran hijau) idealnya berada di sekitar garis hijau tengah (Median 0 SD). Bila berada di bawah garis kuning (-2 SD), konsultasikan dengan dokter atau bidan Posyandu.</span>
        </div>
    </div>

    <!-- Recommended Menu & Feeding Tips for Mother with Interactive Parent AI Generator -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4" x-data="parentAiWidget()">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center space-x-1.5">
                    <i data-lucide="utensils" class="w-4 h-4 text-lime-700"></i>
                    <span>Panduan Nutrisi & Rekomendasi Menu Hari Ini</span>
                </h2>
                <p class="text-xs text-slate-500">Ibu/Orang tua bisa menentukan anggaran belanja harian sendiri dan racik menu bervariasi dengan AI.</p>
            </div>
            <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-lime-50 text-lime-800 font-semibold border border-lime-200 self-start sm:self-auto">
                Gemini Live AI
            </span>
        </div>

        <!-- Parent Budget Input & Live Calculator -->
        <div class="bg-slate-50 p-4 rounded-md border border-slate-200 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div class="flex-1 space-y-2">
                    <label for="parent_budget" class="text-xs font-semibold uppercase tracking-wider text-slate-700 block">
                        Anggaran Belanja Makan Hari Ini (Ketik Bebas Sesuai Keuangan):
                    </label>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <div class="relative rounded-md shadow-xs max-w-xs w-full">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-slate-500 font-bold text-xs">Rp</span>
                            </div>
                            <input type="number" id="parent_budget" step="1000" min="5000" x-model.number="budget"
                                   placeholder="Contoh: 35000"
                                   class="block w-full rounded-md border border-slate-300 pl-9 pr-3 py-2 text-sm font-semibold text-slate-900 focus:border-lime-500 focus:ring-lime-500 bg-white">
                        </div>
                        <div class="flex items-center space-x-1.5 flex-wrap gap-y-1">
                            <span class="text-[11px] text-slate-400 font-medium">Pilihan Cepat:</span>
                            <button type="button" @click="budget = 20000" class="px-2 py-1 text-[11px] rounded bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-medium transition" :class="budget === 20000 ? 'border-lime-500 text-lime-700 bg-lime-50 font-bold' : ''">20rb</button>
                            <button type="button" @click="budget = 35000" class="px-2 py-1 text-[11px] rounded bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-medium transition" :class="budget === 35000 ? 'border-lime-500 text-lime-700 bg-lime-50 font-bold' : ''">35rb</button>
                            <button type="button" @click="budget = 50000" class="px-2 py-1 text-[11px] rounded bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-medium transition" :class="budget === 50000 ? 'border-lime-500 text-lime-700 bg-lime-50 font-bold' : ''">50rb</button>
                            <button type="button" @click="budget = 75000" class="px-2 py-1 text-[11px] rounded bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-medium transition" :class="budget === 75000 ? 'border-lime-500 text-lime-700 bg-lime-50 font-bold' : ''">75rb</button>
                            <button type="button" @click="budget = 100000" class="px-2 py-1 text-[11px] rounded bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-medium transition" :class="budget === 100000 ? 'border-lime-500 text-lime-700 bg-lime-50 font-bold' : ''">100rb</button>
                        </div>
                    </div>
                    <!-- Live Split Indicator -->
                    <div class="flex items-center space-x-2 text-[11px] font-mono bg-white p-2 rounded border border-slate-200 flex-wrap gap-y-1">
                        <span class="text-slate-500 font-sans font-semibold">Alokasi Dibagi 3:</span>
                        <span class="text-lime-800 font-bold">Pagi (~30%): <span x-text="getPagiBudget()"></span></span>
                        <span>•</span>
                        <span class="text-emerald-800 font-bold">Siang (~40%): <span x-text="getSiangBudget()"></span></span>
                        <span>•</span>
                        <span class="text-teal-800 font-bold">Malam (~30%): <span x-text="getMalamBudget()"></span></span>
                    </div>
                </div>

                <div class="shrink-0">
                    <button type="button" @click="generateParentRecommendation()" :disabled="isLoading || !budget || budget < 5000"
                            class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-md bg-lime-600 hover:bg-lime-700 text-white text-xs font-bold transition shadow-sm disabled:opacity-50">
                        <i data-lucide="sparkles" class="w-4 h-4" x-show="!isLoading"></i>
                        <svg x-show="isLoading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                        <span x-text="isLoading ? 'Meracik Menu AI...' : 'Racik Menu Baru Hari Ini (AI)'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Error Banner -->
        <template x-if="errorMessage">
            <div class="p-3.5 rounded-md bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                    <span x-text="errorMessage"></span>
                </div>
                <button type="button" @click="errorMessage = ''" class="text-rose-500 hover:text-rose-800 cursor-pointer">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </template>

        <!-- Rendered Results: Responsive 3-Meal Cards -->
        <template x-if="consultation">
            <div class="space-y-4 pt-1">
                <template x-if="consultation.clinicalAlert">
                    <div class="p-3 rounded-md bg-rose-50 border border-rose-200 text-rose-900 text-xs font-medium flex items-center space-x-2">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                        <span x-text="consultation.clinicalAlert"></span>
                    </div>
                </template>

                <div class="p-3 bg-lime-50/60 rounded-md border border-lime-100 text-xs text-slate-700">
                    <strong class="text-lime-900">Catatan Gizi Anak:</strong>
                    <span x-text="consultation.statusDiagnosis"></span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <template x-for="menu in consultation.recommendedMenu" :key="menu.mealTime">
                        <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200 text-xs space-y-1.5 hover:border-lime-300 transition">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-lime-800 uppercase tracking-wider text-[10px]" x-text="menu.mealTime"></span>
                                <span class="font-mono text-[11px] text-emerald-700 font-semibold" x-text="menu.estimatedCost"></span>
                            </div>
                            <h4 class="font-bold text-slate-900 text-sm" x-text="menu.menuName"></h4>
                            <p class="text-slate-600 text-[11px] leading-relaxed" x-text="menu.ingredients"></p>
                        </div>
                    </template>
                </div>

                <div class="pt-3 border-t border-slate-100 space-y-2">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Tips Pola Asuh & Kebersihan Ibu:</span>
                    <ul class="space-y-1.5 text-xs text-slate-600">
                        <template x-for="(tip, idx) in consultation.parentActionTips" :key="idx">
                            <li class="flex items-start space-x-2">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-lime-600 shrink-0 mt-0.5"></i>
                                <span x-text="tip"></span>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </template>
    </div>

    <!-- Child Immunization History Card for Parents -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-md bg-sky-100 text-sky-700 flex items-center justify-center font-bold">
                    <i data-lucide="shield-plus" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Buku Imunisasi & Vitamin A Ananda</h2>
                    <p class="text-xs text-slate-500">Catatan resmi imunisasi dasar dan suplementasi yang diberikan oleh Posyandu/Puskesmas.</p>
                </div>
            </div>
            <span class="text-xs font-mono px-2 py-0.5 rounded bg-sky-50 text-sky-800 font-semibold border border-sky-200">
                {{ $child->immunizationRecords->count() }} Vaksin/Dosis
            </span>
        </div>

        @if($child->immunizationRecords->isEmpty())
            <div class="p-6 text-center text-slate-400 text-xs">
                <i data-lucide="clipboard" class="w-6 h-6 mx-auto text-slate-300 mb-1.5"></i>
                <p class="font-medium text-slate-600">Belum Ada Riwayat Imunisasi Tercatat</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Bawa buku KIA saat Posyandu untuk didata oleh kader.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            <th class="py-2.5 px-3">Jenis Imunisasi / Suplemen</th>
                            <th class="py-2.5 px-3">Kategori</th>
                            <th class="py-2.5 px-3">Tanggal Diberikan</th>
                            <th class="py-2.5 px-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($child->immunizationRecords as $im)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2.5 px-3 font-semibold text-slate-900">
                                    {{ $im->vaccine_name }}
                                    @if($im->notes)
                                        <div class="text-[10px] text-slate-400 font-normal">{{ $im->notes }}</div>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-medium border
                                        {{ $im->category === 'vitamin_a' ? 'bg-amber-50 text-amber-800 border-amber-200' : ($im->category === 'obat_cacing' ? 'bg-purple-50 text-purple-800 border-purple-200' : 'bg-sky-50 text-sky-800 border-sky-200') }}">
                                        {{ $im->category_label }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 font-mono text-slate-700">
                                    {{ $im->administered_date->translatedFormat('d F Y') }}
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="inline-flex items-center space-x-1 text-emerald-700 text-xs font-semibold">
                                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                                        <span>Lengkap</span>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Educational Videos Banner for Parents -->
    <div class="bg-gradient-to-r from-lime-50 to-emerald-50 border border-lime-200 rounded-lg p-5 shadow-sm space-y-3.5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start space-x-3.5">
                <div class="w-10 h-10 rounded-lg bg-lime-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <i data-lucide="video" class="w-5 h-5"></i>
                </div>
                <div class="space-y-0.5">
                    <h3 class="text-sm font-bold text-slate-900">Tumbuh Academy — Pusat Edukasi Gizi & Parenting</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">Pelajari cara memasak MPASI lokal kaya protein hewani, panduan ASI eksklusif, sanitasi PHBS, dan ikuti kuis pemahaman mandiri.</p>
                </div>
            </div>
            <a href="{{ route('edukasi.index') }}"
               class="shrink-0 inline-flex items-center space-x-2 px-4 py-2.5 rounded-md bg-lime-700 hover:bg-lime-800 text-white text-xs font-bold transition shadow-sm">
                <i data-lucide="play-circle" class="w-4 h-4"></i>
                <span>Buka Semua Modul</span>
            </a>
        </div>

        <!-- Quick Search Bar from Parent Portal -->
        <form action="{{ route('edukasi.index') }}" method="GET" class="flex items-center gap-2 pt-2 border-t border-lime-200/60">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                </div>
                <input type="text" name="search" placeholder="Cari materi edukasi (contoh: MPASI, telur, ASI, sanitasi)..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-md border border-lime-200 bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:border-transparent">
            </div>
            <button type="submit" class="px-3.5 py-2 rounded-md bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold transition shrink-0 cursor-pointer">
                Cari Modul
            </button>
        </form>
    </div>

</div>

@push('scripts')
<script>
    function parentKms() {
        return {
            chartType: 'tb_u',
            chartInstance: null,

            tbCurves: @json($tbuCurves),
            bbCurves: @json($bbuCurves),
            childHeightData: @json($childHeightPoints),
            childWeightData: @json($childWeightPoints),

            init() {
                this.renderChart();
            },

            switchChart(type) {
                this.chartType = type;
                this.renderChart();
            },

            renderChart() {
                const ctx = document.getElementById('parentKmsCanvas');
                if (!ctx) return;

                if (this.chartInstance) {
                    this.chartInstance.destroy();
                }

                const isTb = this.chartType === 'tb_u';
                const ref = isTb ? this.tbCurves : this.bbCurves;
                const childData = isTb ? this.childHeightData : this.childWeightData;
                const unit = isTb ? 'cm' : 'kg';
                const yLabel = isTb ? 'Tinggi Badan (cm)' : 'Berat Badan (kg)';

                this.chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ref.labels,
                        datasets: [
                            {
                                label: '+2 SD (Batas Atas)',
                                data: ref.plus2SD,
                                borderColor: '#94a3b8',
                                borderWidth: 1.5,
                                borderDash: [4, 4],
                                pointRadius: 0,
                                fill: false,
                                tension: 0.3
                            },
                            {
                                label: 'Median 0 SD (Ideal)',
                                data: ref.median,
                                borderColor: '#16A34A',
                                borderWidth: 2,
                                pointRadius: 0,
                                fill: false,
                                tension: 0.3
                            },
                            {
                                label: '-2 SD (Waspada Stunting)',
                                data: ref.minus2SD,
                                borderColor: '#D97706',
                                borderWidth: 1.5,
                                borderDash: [4, 4],
                                pointRadius: 0,
                                fill: false,
                                tension: 0.3
                            },
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
                            {
                                label: 'Pertumbuhan {{ $child->full_name }}',
                                data: childData,
                                borderColor: '#0F172A',
                                borderWidth: 2.5,
                                pointBackgroundColor: '#65A30D',
                                pointBorderColor: '#0F172A',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7,
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
                        scales: {
                            x: {
                                title: { display: true, text: 'Usia (Bulan)', font: { size: 11, family: 'Roboto' } },
                                grid: { color: '#f1f5f9' }
                            },
                            y: {
                                title: { display: true, text: yLabel, font: { size: 11, family: 'Roboto' } },
                                grid: { color: '#f1f5f9' }
                            }
                        }
                    }
                });
            }
        }
    }

    function parentAiWidget() {
        return {
            budget: {{ $latestConsultation ? (int)$latestConsultation->daily_budget : 35000 }},
            isLoading: false,
            consultation: @json($latestConsultation ? $latestConsultation->ai_response : null),
            errorMessage: '',

            formatRupiah(amount) {
                return 'Rp' + new Intl.NumberFormat('id-ID').format(amount || 0);
            },
            getPagiBudget() {
                const b = this.budget || 0;
                return 'Rp' + new Intl.NumberFormat('id-ID').format(Math.round((b * 0.3) / 1000) * 1000);
            },
            getSiangBudget() {
                const b = this.budget || 0;
                return 'Rp' + new Intl.NumberFormat('id-ID').format(Math.round((b * 0.4) / 1000) * 1000);
            },
            getMalamBudget() {
                const b = this.budget || 0;
                const pagi = Math.round((b * 0.3) / 1000) * 1000;
                const siang = Math.round((b * 0.4) / 1000) * 1000;
                return 'Rp' + new Intl.NumberFormat('id-ID').format(Math.max(0, b - pagi - siang));
            },

            async generateParentRecommendation() {
                this.isLoading = true;
                this.errorMessage = '';
                try {
                    const response = await fetch('{{ route("parent.ai-consult", $child->public_token) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            daily_budget: this.budget
                        })
                    });

                    if (response.ok) {
                        const data = await response.json();
                        this.consultation = data.ai_response;
                        this.$nextTick(() => {
                            if (window.lucide) window.lucide.createIcons();
                        });
                    } else {
                        const err = await response.json();
                        this.errorMessage = err.error || 'Terjadi kesalahan saat memproses rekomendasi AI.';
                    }
                } catch (e) {
                    console.error('Error generating parent AI consultation:', e);
                    this.errorMessage = 'Gagal menghubungi layanan AI. Pastikan koneksi internet aktif.';
                } finally {
                    this.isLoading = false;
                }
            }
        }
    }
</script>
@endpush
@endsection
