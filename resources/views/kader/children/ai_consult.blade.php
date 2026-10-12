@extends('layouts.app')

@section('title', 'Rekomendasi Menu Makan & Gizi AI — ' . $child->full_name)
@section('page_title', 'AI Clinical Nutrition Advisor')

@section('page_badge')
    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $child->gender === 'male' ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-pink-50 text-pink-700 border border-pink-200' }}">
        {{ $child->gender_label }} • NIK: {{ $child->nik }}
    </span>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Navigation & Child Biometric Header -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <a href="{{ route('kader.children.show', $child->id) }}" 
               class="inline-flex items-center space-x-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                <i data-lucide="arrow-left" class="w-4 h-4 text-slate-500"></i>
                <span>Kembali ke Grafik KMS & Rekam Gizi</span>
            </a>

            <div class="flex items-center space-x-2">
                <a href="{{ route('kader.children.kms-print', $child->id) }}" target="_blank"
                   class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-md border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition shadow-2xs">
                    <i data-lucide="printer" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>Cetak KMS</span>
                </a>
                <a href="{{ route('parent.portal', ['token' => $child->public_token]) }}" target="_blank"
                   class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-md text-xs font-semibold border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 transition">
                    <i data-lucide="external-link" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>Portal Orang Tua</span>
                </a>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <!-- Child Identity -->
            <div class="space-y-1">
                <div class="flex items-center space-x-2">
                    <h2 class="text-xl font-bold tracking-tight text-slate-900">{{ $child->full_name }}</h2>
                    <a href="{{ route('kader.children.edit', $child->id) }}" class="text-slate-400 hover:text-slate-700" title="Edit Profil">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                    </a>
                </div>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
                    <span>Lahir: <strong class="text-slate-700">{{ $child->date_of_birth->translatedFormat('d F Y') }}</strong></span>
                    <span>Usia: <strong class="text-slate-700">{{ $child->age_in_months }} Bulan</strong></span>
                    <span>BBLR: <strong class="text-slate-700 font-mono">{{ $child->birth_weight }} kg</strong></span>
                    <span>PBL: <strong class="text-slate-700 font-mono">{{ $child->birth_height }} cm</strong></span>
                </div>
                <div class="text-xs text-slate-500">
                    <span>Orang Tua: <strong class="text-slate-700">{{ $child->parent_name }}</strong> ({{ $child->parent_phone }})</span>
                    <span class="mx-1">•</span>
                    <span>Alamat: {{ $child->address }}</span>
                </div>
            </div>

            <!-- Latest Clinical Status -->
            <div class="shrink-0">
                @if($latestMeasurement)
                    <div class="p-3 rounded-md border {{ $latestMeasurement->badge_class }} text-left">
                        <div class="text-[10px] font-semibold uppercase tracking-wider opacity-75">Status Pertumbuhan Terakhir</div>
                        <div class="text-xs font-bold">{{ $latestMeasurement->stunting_status_label }}</div>
                        <div class="text-[11px] font-mono mt-0.5">
                            TB: {{ number_format($latestMeasurement->height, 1) }} cm • BB: {{ number_format($latestMeasurement->weight, 2) }} kg
                        </div>
                        <div class="text-[11px] font-mono">
                            TB/U: {{ $latestMeasurement->zscore_tb_u > 0 ? '+' : '' }}{{ $latestMeasurement->zscore_tb_u }} SD
                            @if($latestMeasurement->is_weight_stagnant)
                                • <span class="font-bold underline text-rose-700">BB Stagnan (2T)</span>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="p-3 rounded-md border border-amber-200 bg-amber-50 text-amber-800 text-xs">
                        <div class="font-bold">Belum Ada Pengukuran</div>
                        <div>Catat penimbangan balita terlebih dahulu.</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if(!$latestMeasurement)
        <!-- Empty Biometrics Notice -->
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-6 text-center space-y-3">
            <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center mx-auto">
                <i data-lucide="scale" class="w-6 h-6"></i>
            </div>
            <h3 class="text-sm font-bold text-amber-900">Belum Ada Data Antropometri Balita</h3>
            <p class="text-xs text-amber-700 max-w-md mx-auto">
                Fitur AI Clinical Nutrition Advisor memerlukan data tinggi badan dan berat badan terbaru untuk menghitung kebutuhan kalori, makronutrien, dan mikronutrien sesuai standar WHO & Kemenkes RI.
            </p>
            <a href="{{ route('kader.children.show', $child->id) }}" 
               class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-md bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Catat Pengukuran di Halaman KMS</span>
            </a>
        </div>
    @else
        <!-- AI Clinical Nutrition Advisor Interactive Generator -->
        <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-5" x-data="aiAdvisorWidget()">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-md bg-lime-100 text-lime-800 flex items-center justify-center font-bold text-sm">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Peracik Menu Gizi &amp; Diagnosis Klinis AI</h3>
                        <p class="text-xs text-slate-500">Konsultasi gizi klinis Kemenkes RI & penyusunan menu lokal berbasis anggaran harian keluarga.</p>
                    </div>
                </div>
                <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-lime-50 text-lime-800 font-semibold border border-lime-200 self-start sm:self-auto">
                    Engine: Google Gemini Live AI
                </span>
            </div>

            <!-- Budget Control Input & Generation Trigger -->
            <div class="bg-slate-50 p-4 rounded-md border border-slate-200 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div class="flex-1 space-y-2">
                        <label for="budget_input" class="text-xs font-semibold uppercase tracking-wider text-slate-700 block">
                            Anggaran Belanja Makan Harian Balita:
                        </label>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="relative rounded-md shadow-xs max-w-xs w-full">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-slate-500 font-bold text-xs">Rp</span>
                                </div>
                                <input type="number" id="budget_input" step="1000" min="5000" x-model.number="budget"
                                       placeholder="Contoh: 35000"
                                       class="block w-full rounded-md border border-slate-300 pl-9 pr-3 py-2 text-sm font-semibold text-slate-900 focus:border-lime-500 focus:ring-lime-500 bg-white">
                            </div>
                            <div class="flex items-center space-x-1.5 flex-wrap gap-y-1">
                                <span class="text-[11px] text-slate-400 font-medium">Pilihan Cepat:</span>
                                <button type="button" @click="budget = 15000" class="px-2 py-1 text-[11px] rounded bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-medium transition" :class="budget === 15000 ? 'border-lime-500 text-lime-700 bg-lime-50 font-bold' : ''">15rb</button>
                                <button type="button" @click="budget = 25000" class="px-2 py-1 text-[11px] rounded bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-medium transition" :class="budget === 25000 ? 'border-lime-500 text-lime-700 bg-lime-50 font-bold' : ''">25rb</button>
                                <button type="button" @click="budget = 50000" class="px-2 py-1 text-[11px] rounded bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-medium transition" :class="budget === 50000 ? 'border-lime-500 text-lime-700 bg-lime-50 font-bold' : ''">50rb</button>
                                <button type="button" @click="budget = 75000" class="px-2 py-1 text-[11px] rounded bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-medium transition" :class="budget === 75000 ? 'border-lime-500 text-lime-700 bg-lime-50 font-bold' : ''">75rb</button>
                                <button type="button" @click="budget = 100000" class="px-2 py-1 text-[11px] rounded bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-medium transition" :class="budget === 100000 ? 'border-lime-500 text-lime-700 bg-lime-50 font-bold' : ''">100rb</button>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500">
                            Gemini AI akan meracik variasi menu sarapan, makan siang, dan makan malam bergizi seimbang sesuai nominal <strong class="text-lime-800 font-bold" x-text="formatRupiah(budget)"></strong>.
                        </p>
                    </div>

                    <div class="shrink-0">
                        <button type="button" @click="generateRecommendation()" :disabled="isLoading || !budget || budget < 5000"
                                class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-md bg-lime-600 hover:bg-lime-700 text-white text-xs font-bold transition shadow-sm disabled:opacity-50">
                            <i data-lucide="sparkles" class="w-4 h-4" x-show="!isLoading"></i>
                            <svg x-show="isLoading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            <span x-text="isLoading ? 'Gemini AI Sedang Meracik Menu...' : 'Generate Rekomendasi Menu Baru (AI)'"></span>
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

            <!-- Rendered Live Results: 3-Part Card -->
            <template x-if="consultation">
                <div class="space-y-4 pt-2">
                    <!-- Clinical Alert Banner if Emergency -->
                    <template x-if="consultation.clinicalAlert">
                        <div class="p-3.5 rounded-md bg-rose-50 border border-rose-200 text-rose-900 text-xs flex items-start space-x-2.5">
                            <i data-lucide="alert-octagon" class="w-4 h-4 text-rose-600 shrink-0 mt-0.5"></i>
                            <div>
                                <span class="font-bold">PERINGATAN KLINIS:</span>
                                <span x-text="consultation.clinicalAlert"></span>
                            </div>
                        </div>
                    </template>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Part 1: Diagnosis & Status -->
                        <div class="bg-white border border-slate-200 rounded-md p-4 space-y-2">
                            <div class="flex items-center space-x-1.5 text-xs font-bold uppercase tracking-wider text-slate-700">
                                <i data-lucide="stethoscope" class="w-4 h-4 text-lime-700"></i>
                                <span>1. Diagnosis Klinis</span>
                            </div>
                            <p class="text-xs text-slate-700 leading-relaxed" x-text="consultation.statusDiagnosis"></p>
                            
                            <div class="pt-3 border-t border-slate-100 text-xs">
                                <span class="font-semibold text-slate-600">Rujukan Puskesmas:</span>
                                <span x-show="consultation.shouldReferToPuskesmas" class="font-bold text-rose-600"> Wajib Dirujuk Segera</span>
                                <span x-show="!consultation.shouldReferToPuskesmas" class="font-medium text-emerald-700"> Pantau Mandiri di Posyandu</span>
                            </div>
                        </div>

                        <!-- Part 2: Tactical Menu Plan -->
                        <div class="bg-white border border-slate-200 rounded-md p-4 space-y-2.5 md:col-span-2">
                            <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-slate-700">
                                <div class="flex items-center space-x-1.5">
                                    <i data-lucide="utensils" class="w-4 h-4 text-lime-700"></i>
                                    <span>2. Menu Harian Pangan Lokal Rendah Biaya</span>
                                </div>
                                <span class="text-[11px] font-mono text-lime-800 font-semibold" x-text="'Budget: ' + formatRupiah(budget)"></span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <template x-for="item in consultation.recommendedMenu" :key="item.mealTime">
                                    <div class="p-2.5 rounded bg-slate-50 border border-slate-200 text-xs space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-slate-900" x-text="item.mealTime"></span>
                                            <span class="font-mono text-[10px] text-emerald-700 font-semibold" x-text="item.estimatedCost"></span>
                                        </div>
                                        <p class="font-semibold text-slate-800" x-text="item.menuName"></p>
                                        <p class="text-[11px] text-slate-600 leading-tight" x-text="item.ingredients"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Part 3: Parent Action Tips -->
                    <div class="bg-white border border-slate-200 rounded-md p-4 space-y-2">
                        <div class="flex items-center space-x-1.5 text-xs font-bold uppercase tracking-wider text-slate-700">
                            <i data-lucide="heart-handshake" class="w-4 h-4 text-lime-700"></i>
                            <span>3. Panduan Tindakan & Pola Asuh Orang Tua</span>
                        </div>
                        <ul class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs text-slate-700">
                            <template x-for="(tip, idx) in consultation.parentActionTips" :key="idx">
                                <li class="flex items-start space-x-2 p-2 rounded bg-lime-50/50 border border-lime-100">
                                    <span class="font-mono font-bold text-lime-700 text-xs shrink-0" x-text="(idx + 1) + '.'"></span>
                                    <span x-text="tip"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </template>

            <!-- Fallback: Display Latest Saved Consultation if not re-generated yet -->
            @if($latestConsultation)
                <div x-show="!consultation" class="pt-2 space-y-3">
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-slate-700 font-bold uppercase tracking-wider flex items-center space-x-1.5">
                            <i data-lucide="history" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span>Hasil Racikan Terakhir ({{ $latestConsultation->created_at->translatedFormat('d M Y, H:i') }})</span>
                        </p>
                        <span class="text-xs font-mono font-semibold text-lime-800 bg-lime-50 px-2 py-0.5 rounded border border-lime-200">
                            Anggaran: Rp{{ number_format($latestConsultation->daily_budget, 0, ',', '.') }}
                        </span>
                    </div>

                    @php $prev = $latestConsultation->ai_response; @endphp
                    @if(isset($prev['clinicalAlert']) && $prev['clinicalAlert'])
                        <div class="p-3 rounded-md bg-rose-50 border border-rose-200 text-rose-900 text-xs flex items-start space-x-2">
                            <i data-lucide="alert-octagon" class="w-4 h-4 text-rose-600 shrink-0 mt-0.5"></i>
                            <div>
                                <span class="font-bold">PERINGATAN KLINIS:</span>
                                <span>{{ $prev['clinicalAlert'] }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-slate-50 border border-slate-200 rounded-md p-4 space-y-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-700 block">1. Diagnosis Klinis</span>
                            <p class="text-xs text-slate-700 leading-relaxed">{{ $prev['statusDiagnosis'] ?? '-' }}</p>
                            <div class="pt-2 border-t border-slate-200 text-xs">
                                <span class="font-semibold text-slate-600">Rujukan:</span>
                                @if(!empty($prev['shouldReferToPuskesmas']))
                                    <span class="font-bold text-rose-600"> Wajib Dirujuk ke Puskesmas</span>
                                @else
                                    <span class="font-medium text-emerald-700"> Pantau Mandiri di Posyandu</span>
                                @endif
                            </div>
                        </div>

                        <div class="bg-slate-50 border border-slate-200 rounded-md p-4 space-y-2 md:col-span-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-700 block">2. Menu Harian</span>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                @foreach($prev['recommendedMenu'] ?? [] as $m)
                                    <div class="p-2.5 rounded bg-white border border-slate-200 text-xs space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-slate-900">{{ $m['mealTime'] ?? '' }}</span>
                                            <span class="font-mono text-[10px] text-emerald-700 font-semibold">{{ $m['estimatedCost'] ?? '' }}</span>
                                        </div>
                                        <p class="font-semibold text-slate-800">{{ $m['menuName'] ?? '' }}</p>
                                        <p class="text-[11px] text-slate-500 leading-tight">{{ $m['ingredients'] ?? '' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if(!empty($prev['parentActionTips']))
                        <div class="bg-slate-50 border border-slate-200 rounded-md p-4 space-y-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-700 block">3. Panduan Tindakan Orang Tua</span>
                            <ul class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-xs text-slate-700">
                                @foreach($prev['parentActionTips'] as $idx => $tip)
                                    <li class="p-2 rounded bg-white border border-slate-200 flex items-start space-x-2">
                                        <span class="font-mono font-bold text-lime-700 shrink-0">{{ $idx + 1 }}.</span>
                                        <span>{{ $tip }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- History of Consultations (Accordion / List) -->
        @if($child->aiConsultations->count() > 1)
            <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-3" x-data="{ openHistory: false }">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900">Riwayat Konsultasi & Racikan Menu Sebelumnya</h4>
                        <p class="text-xs text-slate-500">Terdapat {{ $child->aiConsultations->count() }} catatan konsultasi AI yang tersimpan untuk {{ $child->full_name }}.</p>
                    </div>
                    <button type="button" @click="openHistory = !openHistory" 
                            class="inline-flex items-center space-x-1 text-xs font-semibold text-lime-700 hover:text-lime-800">
                        <span x-text="openHistory ? 'Tutup Riwayat' : 'Lihat Semua Riwayat (' + {{ $child->aiConsultations->count() }} + ')'"></span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform" :class="openHistory ? 'rotate-180' : ''"></i>
                    </button>
                </div>

                <div x-show="openHistory" x-cloak class="space-y-3 pt-3 border-t border-slate-100">
                    @foreach($child->aiConsultations->skip(1) as $c)
                        @php $hist = $c->ai_response; @endphp
                        <div class="p-3.5 rounded-md border border-slate-200 bg-slate-50 text-xs space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800">{{ $c->created_at->translatedFormat('d F Y, H:i') }}</span>
                                <span class="font-mono text-emerald-700 font-semibold">Budget: Rp{{ number_format($c->daily_budget, 0, ',', '.') }}</span>
                            </div>
                            <p class="text-slate-600"><strong>Diagnosis:</strong> {{ $hist['statusDiagnosis'] ?? '-' }}</p>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                @foreach($hist['recommendedMenu'] ?? [] as $hm)
                                    <div class="p-2 rounded bg-white border border-slate-200">
                                        <div class="font-semibold text-slate-800">{{ $hm['mealTime'] ?? '' }}: {{ $hm['menuName'] ?? '' }}</div>
                                        <div class="text-[10px] text-slate-500">{{ $hm['ingredients'] ?? '' }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

</div>

@push('scripts')
<script>
    function aiAdvisorWidget() {
        return {
            budget: 25000,
            isLoading: false,
            consultation: null,
            errorMessage: '',

            formatRupiah(amount) {
                return 'Rp' + new Intl.NumberFormat('id-ID').format(amount || 0);
            },

            async generateRecommendation() {
                this.isLoading = true;
                this.errorMessage = '';
                try {
                    const response = await fetch('{{ route("kader.children.ai-consult", $child->id) }}', {
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
                            if (window.lucide && window.lucide.createIcons) {
                                window.lucide.createIcons();
                            }
                        });
                    } else {
                        const err = await response.json();
                        this.errorMessage = err.error || 'Terjadi kesalahan saat memproses AI consultation.';
                    }
                } catch (e) {
                    console.error('Error generating AI consultation:', e);
                    this.errorMessage = 'Gagal menghubungi layanan konsultasi AI. Pastikan koneksi aktif.';
                } finally {
                    this.isLoading = false;
                }
            }
        }
    }
</script>
@endpush
@endsection
