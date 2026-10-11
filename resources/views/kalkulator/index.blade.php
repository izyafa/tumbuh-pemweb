@extends(Auth::check() ? 'layouts.app' : 'layouts.guest')

@section('title', 'Kalkulator Z-Score WHO & Deteksi Stunting Instan')
@section('page_title', 'Kalkulator Gizi & Deteksi Dini Stunting')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6 space-y-6" x-data="growthCalculator()">

    <!-- Header Card -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 sm:p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-lime-50 text-lime-800 border border-lime-200 mb-2">
                    <i data-lucide="calculator" class="w-3.5 h-3.5 text-lime-700"></i>
                    <span>Standar Baku WHO & Permenkes RI No. 2/2020</span>
                </div>
                <h1 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900">Kalkulator Z-Score Pertumbuhan Balita</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                    Periksa status stunting, risiko kurus (*wasting*), dan berat badan balita Anda secara instan tanpa perlu mendaftar atau login.
                </p>
            </div>
            <div class="shrink-0 flex items-center space-x-2">
                <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-600 text-xs font-mono font-medium">Usia 0–60 Bulan</span>
            </div>
        </div>
    </div>

    <!-- Main Grid: Input Form & Live Result -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Input Form Card (5 cols) -->
        <div class="lg:col-span-5 bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700 border-b border-slate-100 pb-2 flex items-center space-x-1.5">
                <i data-lucide="sliders" class="w-4 h-4 text-lime-600"></i>
                <span>Data Antropometri Balita</span>
            </h2>

            <form @submit.prevent="calculate()" class="space-y-4 text-xs">
                <!-- Gender Selection -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Jenis Kelamin Anak <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" 
                                @click="gender = 'male'; calculate()"
                                :class="gender === 'male' ? 'bg-sky-50 border-sky-400 text-sky-800 font-bold ring-1 ring-sky-400' : 'bg-slate-50 border-slate-200 text-slate-600'"
                                class="py-2.5 px-3 rounded-md border text-center transition flex items-center justify-center space-x-1.5 cursor-pointer">
                            <span>👦 Laki-laki</span>
                        </button>
                        <button type="button" 
                                @click="gender = 'female'; calculate()"
                                :class="gender === 'female' ? 'bg-pink-50 border-pink-400 text-pink-800 font-bold ring-1 ring-pink-400' : 'bg-slate-50 border-slate-200 text-slate-600'"
                                class="py-2.5 px-3 rounded-md border text-center transition flex items-center justify-center space-x-1.5 cursor-pointer">
                            <span>👧 Perempuan</span>
                        </button>
                    </div>
                </div>

                <!-- Age input mode -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="font-semibold text-slate-700">Usia Anak (Bulan) <span class="text-rose-500">*</span></label>
                        <span class="text-[11px] text-slate-400 font-mono" x-text="ageInMonths + ' Bulan'"></span>
                    </div>
                    <input type="range" min="0" max="60" x-model.number="ageInMonths" @input="calculate()"
                           class="w-full accent-lime-600 cursor-pointer">
                    <div class="flex justify-between text-[10px] text-slate-400 font-mono mt-1">
                        <span>0 Bulan (Lahir)</span>
                        <span>24 Bln (2 Thn)</span>
                        <span>60 Bln (5 Thn)</span>
                    </div>
                </div>

                <!-- Height / Length -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Panjang / Tinggi Badan (cm) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input type="number" step="0.1" min="30" max="150" x-model.number="height" @input.debounce.300ms="calculate()"
                               class="w-full px-3 py-2 rounded-md border border-slate-300 text-sm font-mono text-slate-900 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600 tabular-nums"
                               placeholder="Contoh: 75.5">
                        <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-medium">cm</span>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-0.5">Usia &lt; 2 thn diukur telentang (PB), &ge; 2 thn diukur berdiri (TB).</p>
                </div>

                <!-- Weight -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Berat Badan (kg) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input type="number" step="0.05" min="1" max="50" x-model.number="weight" @input.debounce.300ms="calculate()"
                               class="w-full px-3 py-2 rounded-md border border-slate-300 text-sm font-mono text-slate-900 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600 tabular-nums"
                               placeholder="Contoh: 9.8">
                        <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-medium">kg</span>
                    </div>
                </div>

                <!-- Quick Presets -->
                <div class="pt-2 border-t border-slate-100">
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block mb-1">Contoh Kasus Klinis Cepat:</span>
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" @click="setPreset('normal')" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-600 text-[10px] font-medium transition cursor-pointer">
                            ✅ 12 Bln Normal
                        </button>
                        <button type="button" @click="setPreset('stunted')" class="px-2 py-1 rounded bg-amber-50 hover:bg-amber-100 text-amber-800 text-[10px] font-medium transition cursor-pointer">
                            ⚠️ 18 Bln Pendek
                        </button>
                        <button type="button" @click="setPreset('severely_stunted')" class="px-2 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-800 text-[10px] font-medium transition cursor-pointer">
                            🚨 24 Bln Sangat Pendek
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right: Live Result & Clinical Analysis (7 cols) -->
        <div class="lg:col-span-7 space-y-4">

            <!-- Result Card -->
            <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-5"
                 :class="{'border-rose-300 ring-1 ring-rose-200': result.alert_level === 'danger', 'border-amber-300 ring-1 ring-amber-200': result.alert_level === 'warning', 'border-emerald-300': result.alert_level === 'normal'}">
                
                <!-- Status Header Banner -->
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-1">
                        <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Diagnosis Klinis Instan</span>
                        <h3 class="text-base sm:text-lg font-bold"
                            :class="{'text-rose-700': result.alert_level === 'danger', 'text-amber-700': result.alert_level === 'warning', 'text-emerald-700': result.alert_level === 'normal'}"
                            x-text="result.summary_title"></h3>
                        <p class="text-xs text-slate-600 leading-relaxed" x-text="result.recommendation"></p>
                    </div>
                    <span class="p-2.5 rounded-full shrink-0"
                          :class="{'bg-rose-100 text-rose-700': result.alert_level === 'danger', 'bg-amber-100 text-amber-700': result.alert_level === 'warning', 'bg-emerald-100 text-emerald-700': result.alert_level === 'normal'}">
                        <i data-lucide="activity" class="w-5 h-5"></i>
                    </span>
                </div>

                <!-- 3 Antropometric Indicator Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    
                    <!-- TB/U (Stunting) -->
                    <div class="p-3.5 rounded-lg border bg-slate-50/50 space-y-1.5"
                         :class="getStatusCardClass(result.tb_u?.status)">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">TB / U (Stunting)</span>
                            <span class="text-xs font-mono font-bold" x-text="(result.tb_u?.zScore > 0 ? '+' : '') + result.tb_u?.zScore + ' SD'"></span>
                        </div>
                        <div class="text-xs font-bold text-slate-900" x-text="result.tb_u?.label"></div>
                        <p class="text-[10px] text-slate-500 leading-tight">Indikator pertumbuhan linier jangka panjang.</p>
                    </div>

                    <!-- BB/U (Underweight) -->
                    <div class="p-3.5 rounded-lg border bg-slate-50/50 space-y-1.5"
                         :class="getStatusCardClass(result.bb_u?.status)">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">BB / U (Berat)</span>
                            <span class="text-xs font-mono font-bold" x-text="(result.bb_u?.zScore > 0 ? '+' : '') + result.bb_u?.zScore + ' SD'"></span>
                        </div>
                        <div class="text-xs font-bold text-slate-900" x-text="result.bb_u?.label"></div>
                        <p class="text-[10px] text-slate-500 leading-tight">Indikator kecukupan berat badan total.</p>
                    </div>

                    <!-- BB/TB (Wasting) -->
                    <div class="p-3.5 rounded-lg border bg-slate-50/50 space-y-1.5"
                         :class="getStatusCardClass(result.bb_tb?.status)">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">BB / TB (Gizi)</span>
                            <span class="text-xs font-mono font-bold" x-text="(result.bb_tb?.zScore > 0 ? '+' : '') + result.bb_tb?.zScore + ' SD'"></span>
                        </div>
                        <div class="text-xs font-bold text-slate-900" x-text="result.bb_tb?.label"></div>
                        <p class="text-[10px] text-slate-500 leading-tight">Deteksi gizi buruk & kurus akut.</p>
                    </div>
                </div>

                <!-- Call to action boxes -->
                <div class="bg-slate-50 p-3.5 rounded-lg border border-slate-200 text-xs text-slate-600 space-y-2">
                    <div class="font-semibold text-slate-800 flex items-center space-x-1.5">
                        <i data-lucide="lightbulb" class="w-4 h-4 text-amber-500"></i>
                        <span>Langkah Selanjutnya:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-slate-600 pl-1">
                        <li>Bawa anak ke jadwal Posyandu terdekat untuk penimbangan resmi dan pencatatan KMS.</li>
                        <li>Pastikan anak mendapatkan minimal 1 porsi protein hewani (telur / hati ayam / ikan) setiap kali makan utama.</li>
                        <li>Akses materi lengkap pencegahan stunting di <a href="{{ route('edukasi.index') }}" class="font-semibold text-lime-700 underline">Tumbuh Academy</a>.</li>
                    </ul>
                </div>
            </div>

            <!-- Reference Scale Legend -->
            <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm text-xs space-y-2">
                <span class="font-bold text-slate-800 uppercase tracking-wider text-[11px] block">Pedoman Batas Ambang Z-Score WHO:</span>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-[11px]">
                    <div class="p-2 rounded bg-rose-50 border border-rose-200 text-rose-800">
                        <strong>&lt; -3.0 SD:</strong> Sangat Pendek / Gizi Buruk (Rujukan Dokter Spesialis)
                    </div>
                    <div class="p-2 rounded bg-amber-50 border border-amber-200 text-amber-800">
                        <strong>-3.0 s/d &lt; -2.0 SD:</strong> Pendek / Kurus (Intervensi PMT & Protein Hewani)
                    </div>
                    <div class="p-2 rounded bg-emerald-50 border border-emerald-200 text-emerald-800">
                        <strong>-2.0 s/d +2.0 SD:</strong> Pertumbuhan Normal (Pertahankan Pola Asuh)
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function growthCalculator() {
    return {
        gender: 'male',
        ageInMonths: 14,
        height: 77.0,
        weight: 9.8,
        result: {
            alert_level: 'normal',
            summary_title: 'Pertumbuhan Baik & Sesuai Baku WHO',
            recommendation: 'Lanjutkan pemberian gizi seimbang kaya protein hewani dan bawa anak rutin ke Posyandu setiap bulan.',
            tb_u: { zScore: 0.1, status: 'normal', label: 'Normal' },
            bb_u: { zScore: 0.0, status: 'normal', label: 'Berat Badan Normal' },
            bb_tb: { zScore: 0.0, status: 'normal', label: 'Gizi Baik (Normal)' },
        },
        init() {
            this.calculate();
        },
        setPreset(type) {
            if (type === 'normal') {
                this.ageInMonths = 12;
                this.height = 76.0;
                this.weight = 9.6;
                this.gender = 'male';
            } else if (type === 'stunted') {
                this.ageInMonths = 18;
                this.height = 74.5;
                this.weight = 8.5;
                this.gender = 'female';
            } else if (type === 'severely_stunted') {
                this.ageInMonths = 24;
                this.height = 78.0;
                this.weight = 9.0;
                this.gender = 'male';
            }
            this.calculate();
        },
        calculate() {
            if (!this.height || !this.weight) return;

            fetch('{{ route('kalkulator.calculate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    gender: this.gender,
                    age_in_months: this.ageInMonths,
                    height: parseFloat(this.height),
                    weight: parseFloat(this.weight),
                })
            })
            .then(res => res.json())
            .then(data => {
                this.result = data;
                this.$nextTick(() => {
                    if (window.lucide && window.lucide.createIcons) window.lucide.createIcons();
                });
            })
            .catch(err => {
                console.error('Calculation error:', err);
            });
        },
        getStatusCardClass(status) {
            if (status === 'severely_stunted' || status === 'severely_wasted' || status === 'severely_underweight') {
                return 'border-rose-300 bg-rose-50/50 text-rose-900';
            }
            if (status === 'stunted' || status === 'wasted' || status === 'underweight') {
                return 'border-amber-300 bg-amber-50/50 text-amber-900';
            }
            return 'border-emerald-200 bg-emerald-50/30 text-emerald-900';
        }
    }
}
</script>
@endpush
