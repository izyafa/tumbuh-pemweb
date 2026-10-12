@extends('layouts.guest')

@section('title', 'Rekomendasi Menu Makan Hari Ini — ' . $child->full_name)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 space-y-6">

    <!-- Top Navigation & Mother-Friendly Header -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <a href="{{ route('parent.portal', $child->public_token) }}" 
               class="inline-flex items-center space-x-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
                <i data-lucide="arrow-left" class="w-4 h-4 text-slate-500"></i>
                <span>Kembali ke Grafik Pemantauan KMS</span>
            </a>

            <div class="flex items-center space-x-2">
                <a href="{{ route('parent.kms-print', $child->public_token) }}" target="_blank"
                   class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-md border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition shadow-2xs">
                    <i data-lucide="printer" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>Cetak KMS</span>
                </a>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-lime-50 text-lime-800 border border-lime-200">
                    <i data-lucide="utensils" class="w-3 h-3 text-lime-600"></i>
                    <span>Panduan Nutrisi Balita</span>
                </span>
                <h1 class="text-xl font-bold tracking-tight text-slate-900">{{ $child->full_name }}</h1>
                <p class="text-xs text-slate-500">
                    Lahir: {{ $child->date_of_birth->translatedFormat('d F Y') }} • Usia: <strong>{{ $child->age_in_months }} Bulan</strong> • {{ $child->gender_label }}
                </p>
            </div>

            <!-- Current Biometrics Summary -->
            <div class="shrink-0 flex items-center gap-2">
                @if($latestMeasurement)
                    <div class="px-3 py-2 rounded-md border {{ $latestMeasurement->badge_class }} text-left">
                        <div class="text-[10px] font-semibold uppercase tracking-wider opacity-75">Status Gizi Terakhir</div>
                        <div class="text-xs font-bold">{{ $latestMeasurement->stunting_status_label }}</div>
                        <div class="text-[11px] font-mono mt-0.5">
                            TB: {{ number_format($latestMeasurement->height, 1) }} cm • BB: {{ number_format($latestMeasurement->weight, 2) }} kg
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if(!$latestMeasurement)
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-6 text-center space-y-3">
            <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center mx-auto">
                <i data-lucide="info" class="w-6 h-6"></i>
            </div>
            <h3 class="text-sm font-bold text-amber-900">Belum Ada Catatan Penimbangan Balita</h3>
            <p class="text-xs text-amber-700 max-w-md mx-auto">
                Bawalah ananda ke jadwal Posyandu terdekat untuk ditimbang dan diukur. Setelah kader mencatat data antropometri, AI akan secara otomatis meracik saran menu gizi terbaik untuk si kecil.
            </p>
            <a href="{{ route('parent.portal', $child->public_token) }}" 
               class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-md bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Halaman Pantau</span>
            </a>
        </div>
    @else
        <!-- Interactive Parent AI Generator Widget -->
        <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4" x-data="parentAiWidget()">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center space-x-1.5">
                        <i data-lucide="sparkles" class="w-4 h-4 text-lime-700"></i>
                        <span>Racik Menu Makan Harian Sesuai Anggaran</span>
                    </h2>
                    <p class="text-xs text-slate-500">Ibu bisa menentukan anggaran belanja harian sendiri dan AI akan meracik variasi menu pangan lokal bergizi.</p>
                </div>
                <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-lime-50 text-lime-800 font-semibold border border-lime-200 self-start sm:self-auto">
                    Gemini Live AI
                </span>
            </div>

            <!-- Parent Budget Input & Live Split Indicator -->
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

            <!-- Rendered Live Results -->
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

            <!-- Fallback: Display Saved Menu if not re-generated yet in this session -->
            @if($latestConsultation)
                <div x-show="!consultation" class="pt-1 space-y-3">
                    @php $saved = $latestConsultation->ai_response; @endphp
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center space-x-1.5">
                            <i data-lucide="bookmark-check" class="w-3.5 h-3.5 text-lime-700"></i>
                            <span>Menu Terakhir yang Dirujuk ({{ $latestConsultation->created_at->translatedFormat('d M Y, H:i') }})</span>
                        </div>
                        <span class="text-xs font-mono font-semibold text-lime-800 bg-lime-50 px-2 py-0.5 rounded border border-lime-200">
                            Budget: Rp{{ number_format($latestConsultation->daily_budget, 0, ',', '.') }}
                        </span>
                    </div>

                    @if(!empty($saved['clinicalAlert']))
                        <div class="p-3 rounded-md bg-rose-50 border border-rose-200 text-rose-900 text-xs font-medium flex items-center space-x-2">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                            <span>{{ $saved['clinicalAlert'] }}</span>
                        </div>
                    @endif

                    <div class="p-3 bg-lime-50/60 rounded-md border border-lime-100 text-xs text-slate-700">
                        <strong class="text-lime-900">Catatan Gizi:</strong>
                        <span>{{ $saved['statusDiagnosis'] ?? '-' }}</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach($saved['recommendedMenu'] ?? [] as $m)
                            <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200 text-xs space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-lime-800 uppercase tracking-wider text-[10px]">{{ $m['mealTime'] ?? '' }}</span>
                                    <span class="font-mono text-[11px] text-emerald-700 font-semibold">{{ $m['estimatedCost'] ?? '' }}</span>
                                </div>
                                <h4 class="font-bold text-slate-900 text-sm">{{ $m['menuName'] ?? '' }}</h4>
                                <p class="text-slate-600 text-[11px] leading-relaxed">{{ $m['ingredients'] ?? '' }}</p>
                            </div>
                        @endforeach
                    </div>

                    @if(!empty($saved['parentActionTips']))
                        <div class="pt-3 border-t border-slate-100 space-y-2">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Tips Pola Asuh & Kebersihan:</span>
                            <ul class="space-y-1.5 text-xs text-slate-600">
                                @foreach($saved['parentActionTips'] as $tip)
                                    <li class="flex items-start space-x-2">
                                        <i data-lucide="check" class="w-3.5 h-3.5 text-lime-600 shrink-0 mt-0.5"></i>
                                        <span>{{ $tip }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- History of Previous Meal Generations for Parents -->
        @if($child->aiConsultations->count() > 1)
            <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-3" x-data="{ openHistory: false }">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900">Riwayat Racikan Menu Sebelumnya</h4>
                        <p class="text-xs text-slate-500">Ibu bisa melihat kembali menu-menu yang pernah diracik sebelumnya.</p>
                    </div>
                    <button type="button" @click="openHistory = !openHistory" 
                            class="inline-flex items-center space-x-1 text-xs font-semibold text-lime-700 hover:text-lime-800">
                        <span x-text="openHistory ? 'Tutup Riwayat' : 'Buka Riwayat (' + {{ $child->aiConsultations->count() }} + ')'"></span>
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
    function parentAiWidget() {
        return {
            budget: 35000,
            isLoading: false,
            consultation: null,
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
                            if (window.lucide && window.lucide.createIcons) {
                                window.lucide.createIcons();
                            }
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
