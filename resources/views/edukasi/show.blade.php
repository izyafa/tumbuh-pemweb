@extends(Auth::check() ? 'layouts.app' : 'layouts.guest')

@section('title', $video->title . ' — Tumbuh Academy')
@section('page_title', 'Modul Edukasi')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="showVideoApp()">

    <!-- Top Breadcrumb & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <nav class="flex items-center space-x-2 text-xs text-slate-500">
            <a href="{{ route('edukasi.index') }}" class="hover:text-lime-700 transition flex items-center space-x-1 font-medium">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kembali ke Katalog Tumbuh Academy</span>
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-700 font-semibold truncate max-w-xs">{{ $video->category }}</span>
        </nav>

        <div class="flex items-center space-x-2">
            <!-- Share WhatsApp Button -->
            @php
                $shareText = rawurlencode("Halo Bunda/Kader, mari tonton video edukasi gizi dan stunting: \"{$video->title}\"\n\nTonton selengkapnya di Tumbuh Academy:\n" . url()->current());
            @endphp
            <a href="https://api.whatsapp.com/send?text={{ $shareText }}" target="_blank"
               class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition">
                <i data-lucide="message-circle-more" class="w-3.5 h-3.5"></i>
                <span>Bagikan ke WhatsApp</span>
            </a>

            @if(Auth::check() && Auth::user()->isAdmin())
                <a href="{{ route('admin.videos') }}" class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-md border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 text-xs font-medium shadow-xs transition">
                    <i data-lucide="settings" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>Kelola</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Main Content Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left 2 Cols: Cinema Player & Takeaways & Interactive Quiz -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Video Player Box -->
            <div class="bg-black rounded-xl overflow-hidden shadow-md border border-slate-800">
                <div class="relative w-full aspect-video">
                    <iframe src="{{ $video->youtube_embed_url }}?autoplay=1&rel=0&enablejsapi=1" 
                            title="{{ $video->title }}" 
                            class="w-full h-full border-0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                            allowfullscreen></iframe>
                </div>
            </div>

            <!-- YouTube Direct Link & Helper Notice -->
            <div class="flex items-center justify-between text-xs text-slate-500 px-1 -mt-3">
                <span class="flex items-center space-x-1">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-lime-600"></i>
                    <span>Materi Resmi Kemenkes RI / UNICEF</span>
                </span>
                <a href="{{ $video->youtube_url }}" target="_blank"
                   class="text-rose-600 hover:text-rose-700 font-semibold flex items-center space-x-1.5 hover:underline">
                    <i data-lucide="youtube" class="w-4 h-4 text-rose-600"></i>
                    <span>Tonton Langsung di YouTube &rarr;</span>
                </a>
            </div>

            <!-- Video Details Header -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-lime-100 text-lime-800 border border-lime-200 uppercase tracking-wide">
                        {{ $video->category }}
                    </span>
                    <span class="inline-flex items-center space-x-1 text-slate-400 text-xs">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                        <span>Dipublikasikan: {{ $video->created_at->format('d M Y') }}</span>
                    </span>
                    <span class="inline-flex items-center space-x-1 text-emerald-700 font-medium text-xs bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-600"></i>
                        <span>Materi Terverifikasi Kemenkes RI / WHO</span>
                    </span>
                </div>

                <h1 class="text-lg sm:text-xl font-bold text-slate-900 leading-snug">
                    {{ $video->title }}
                </h1>

                <!-- Takeaways / Intisari Praktis -->
                <div class="pt-3 border-t border-slate-100">
                    <div class="flex items-center space-x-2 mb-2 text-slate-800 font-bold text-xs uppercase tracking-wider">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-lime-600"></i>
                        <span>Intisari Praktis & Panduan Medis:</span>
                    </div>
                    <div class="p-4 rounded-lg bg-slate-50 border border-slate-100 text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                        {{ $video->summary }}
                    </div>
                </div>
            </div>

            <!-- Interactive Quiz Section -->
            @if(!empty($video->quiz_data))
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center space-x-2">
                            <div class="w-7 h-7 rounded-lg bg-lime-100 text-lime-800 flex items-center justify-center font-bold text-sm">
                                ?
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Uji Pemahaman Mandiri</h3>
                                <p class="text-xs text-slate-500">Jawab pertanyaan di bawah ini untuk mengukur pemahaman materi setelah menonton video.</p>
                            </div>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 border border-slate-200">
                            {{ count($video->quiz_data) }} Pertanyaan
                        </span>
                    </div>

                    <!-- Questions List -->
                    <div class="space-y-4 text-xs">
                        @foreach($video->quiz_data as $qIndex => $q)
                            <div class="p-4 rounded-lg border border-slate-200 space-y-2.5"
                                 :class="{
                                     'bg-slate-50': !submitted,
                                     'bg-emerald-50/50 border-emerald-200': submitted && userAnswers[{{ $qIndex }}] === {{ $q['correct_index'] }},
                                     'bg-rose-50/50 border-rose-200': submitted && userAnswers[{{ $qIndex }}] !== {{ $q['correct_index'] }}
                                 }">
                                <p class="font-bold text-slate-900 text-xs sm:text-sm">
                                    <span>{{ $qIndex + 1 }}. </span>
                                    <span>{{ $q['question'] }}</span>
                                </p>

                                <div class="space-y-1.5 pl-2">
                                    @foreach($q['options'] as $oIndex => $opt)
                                        <label class="flex items-start space-x-2 p-2 rounded cursor-pointer transition text-slate-700"
                                               :class="{
                                                   'hover:bg-slate-100': !submitted,
                                                   'bg-emerald-100 text-emerald-900 font-bold': submitted && {{ $oIndex }} === {{ $q['correct_index'] }},
                                                   'bg-rose-100 text-rose-900 line-through': submitted && userAnswers[{{ $qIndex }}] === {{ $oIndex }} && {{ $oIndex }} !== {{ $q['correct_index'] }}
                                               }">
                                            <input type="radio" 
                                                   name="question_{{ $qIndex }}" 
                                                   :value="{{ $oIndex }}" 
                                                   x-model.number="userAnswers[{{ $qIndex }}]"
                                                   :disabled="submitted"
                                                   class="mt-0.5 text-lime-600 focus:ring-lime-500">
                                            <span class="text-xs leading-relaxed">{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                <!-- Explanation on Submit -->
                                <template x-if="submitted">
                                    <div class="mt-2 pt-2 border-t text-xs leading-relaxed"
                                         :class="userAnswers[{{ $qIndex }}] === {{ $q['correct_index'] }} ? 'border-emerald-200 text-emerald-800' : 'border-rose-200 text-rose-800'">
                                        <strong>Penjelasan Klinis:</strong> {{ $q['explanation'] }}
                                    </div>
                                </template>
                            </div>
                        @endforeach

                        <!-- Score Result Summary -->
                        <template x-if="submitted">
                            <div class="p-4 rounded-lg bg-slate-900 text-white text-center space-y-1.5 shadow-sm">
                                <span class="text-xs font-semibold uppercase tracking-wider text-lime-400">Hasil Evaluasi Mandiri:</span>
                                <div class="text-xl font-bold font-mono text-white" x-text="scoreText"></div>
                                <p class="text-[11px] text-slate-300">Hasil kuis ini dapat Anda gunakan untuk mengevaluasi pemahaman kader dan orang tua balita.</p>
                            </div>
                        </template>

                        <!-- Actions -->
                        <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
                            <button type="button" x-show="!submitted" @click="submitQuiz()" 
                                    class="px-5 py-2 rounded-lg bg-lime-600 hover:bg-lime-700 text-white font-semibold text-xs transition shadow-xs">
                                Kirim Jawaban Kuis
                            </button>
                            <button type="button" x-show="submitted" @click="resetQuiz()" 
                                    class="px-5 py-2 rounded-lg bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs transition shadow-xs">
                                Ulangi Kuis
                            </button>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <!-- Right Col: Related Modules & Resources -->
        <div class="space-y-6">

            <!-- Author & Source Card -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm space-y-3">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-lime-100 text-lime-800 flex items-center justify-center font-bold text-sm shrink-0">
                        <i data-lucide="tv-2" class="w-5 h-5 text-lime-700"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Tumbuh Academy</h4>
                        <p class="text-[11px] text-slate-500">Pusat Pembelajaran Digital Posyandu</p>
                    </div>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Setiap video dikurasi dari institusi kesehatan resmi untuk mendukung program percepatan penurunan stunting nasional (Ran-Pasti).
                </p>
                <div class="pt-2 border-t border-slate-100">
                    <a href="{{ $video->youtube_url }}" target="_blank"
                       class="inline-flex items-center space-x-1.5 text-xs text-rose-600 hover:text-rose-700 font-semibold transition">
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                        <span>Buka langsung di YouTube &rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Related Videos List -->
            @if(isset($relatedVideos) && $relatedVideos->isNotEmpty())
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Modul Terkait Lainnya</h4>
                        <a href="{{ route('edukasi.index') }}" class="text-[11px] text-lime-700 hover:underline font-semibold">
                            Semua Modul &rarr;
                        </a>
                    </div>

                    <div class="space-y-3">
                        @foreach($relatedVideos as $rel)
                            <a href="{{ route('edukasi.show', $rel->id) }}" 
                               class="group block p-2.5 rounded-lg border border-slate-100 hover:border-lime-300 hover:bg-lime-50/30 transition">
                                <div class="flex space-x-3">
                                    <div class="relative w-24 h-16 rounded overflow-hidden bg-slate-100 shrink-0 border border-slate-200">
                                        <img src="{{ $rel->youtube_thumbnail_url }}" 
                                             alt="{{ $rel->title }}" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition duration-200"
                                             loading="lazy">
                                        <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition flex items-center justify-center">
                                            <i data-lucide="play" class="w-4 h-4 text-white drop-shadow"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="inline-block px-1.5 py-0.2 rounded text-[9px] font-semibold bg-slate-100 text-slate-600 group-hover:bg-lime-100 group-hover:text-lime-800 transition">
                                            {{ $rel->category }}
                                        </span>
                                        <h5 class="text-xs font-bold text-slate-900 group-hover:text-lime-700 transition line-clamp-2 mt-1 leading-snug">
                                            {{ $rel->title }}
                                        </h5>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>

</div>

@push('scripts')
<script>
    function showVideoApp() {
        return {
            userAnswers: {},
            submitted: false,
            scoreText: '',
            quizData: @js($video->quiz_data ?? []),

            submitQuiz() {
                if (!this.quizData || this.quizData.length === 0) return;
                let correctCount = 0;
                const total = this.quizData.length;

                this.quizData.forEach((q, idx) => {
                    if (this.userAnswers[idx] === q.correct_index) {
                        correctCount++;
                    }
                });

                this.submitted = true;
                this.scoreText = correctCount + ' dari ' + total + ' Benar (' + Math.round((correctCount / total) * 100) + '%)';
                this.$nextTick(() => {
                    if (window.lucide && window.lucide.createIcons) window.lucide.createIcons();
                });
            },

            resetQuiz() {
                this.userAnswers = {};
                this.submitted = false;
                this.scoreText = '';
            }
        }
    }
</script>
@endpush
@endsection
