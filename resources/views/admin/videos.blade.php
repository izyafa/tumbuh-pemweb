@extends('layouts.app')

@section('title', 'Kelola Video & Kuis Tumbuh Academy')
@section('page_title', 'Manajemen Modul Edukasi & Kuis')

@section('page_badge')
    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-50 text-purple-800 border border-purple-200">
        Panel Administrator & Dokter
    </span>
@endsection

@section('content')
<div class="space-y-6" x-data="videoManager()">

    <!-- Header Actions -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-lime-600"></span>
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Modul Pembelajaran & Kuis Interaktif</h2>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Kelola materi literasi gizi, video edukasi YouTube, dan buat kuis pemahaman mandiri secara visual tanpa perlu menulis kode JSON.
            </p>
        </div>
        <div class="flex items-center space-x-2 shrink-0">
            <a href="{{ route('edukasi.index') }}" target="_blank"
               class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-md border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium transition cursor-pointer">
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                <span>Lihat Katalog Academy</span>
            </a>
            <button type="button" @click="openCreateModal()"
                    class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-md bg-lime-600 hover:bg-lime-700 text-white text-xs font-semibold shadow-sm transition cursor-pointer">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Tambah Modul Video</span>
            </button>
        </div>
    </div>

    <!-- Video Modules Table -->
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4 min-w-[260px]">Judul Modul & Ringkasan</th>
                        <th class="py-3 px-3">Kategori</th>
                        <th class="py-3 px-3">Tautan YouTube</th>
                        <th class="py-3 px-3 text-center">Kuis Pemahaman</th>
                        <th class="py-3 px-3">Tanggal Dibuat</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($videos as $vid)
                        @php
                            $quizCount = !empty($vid->quiz_data) && is_array($vid->quiz_data) ? count($vid->quiz_data) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3 px-4 font-semibold text-slate-900">
                                <div class="flex items-center space-x-3">
                                    <div class="relative w-16 h-10 rounded overflow-hidden bg-slate-100 shrink-0 border border-slate-200">
                                        <img src="{{ $vid->youtube_thumbnail_url }}" alt="" class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('edukasi.show', $vid->id) }}" target="_blank" class="hover:text-lime-700 transition flex items-center space-x-1">
                                            <span class="truncate">{{ $vid->title }}</span>
                                            <i data-lucide="external-link" class="w-3 h-3 text-slate-400 shrink-0"></i>
                                        </a>
                                        <p class="text-[11px] font-normal text-slate-500 line-clamp-1 mt-0.5">{{ Str::limit($vid->summary, 80) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-lime-50 text-lime-800 border border-lime-200">
                                    {{ $vid->category }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-slate-500 font-mono text-[11px]">
                                <a href="{{ $vid->youtube_url }}" target="_blank" class="text-lime-700 hover:underline inline-flex items-center space-x-1">
                                    <i data-lucide="youtube" class="w-3.5 h-3.5 text-rose-600 shrink-0"></i>
                                    <span>Tonton</span>
                                </a>
                            </td>
                            <td class="py-3 px-3 text-center">
                                @if($quizCount > 0)
                                    <button type="button" @click="openQuickQuizPreview(@js($vid))"
                                            class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition cursor-pointer"
                                            title="Klik untuk melihat pratinjau soal kuis">
                                        <i data-lucide="help-circle" class="w-3 h-3"></i>
                                        <span>{{ $quizCount }} Soal</span>
                                    </button>
                                @else
                                    <span class="text-slate-400 text-[11px] italic">Tidak ada kuis</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-slate-400 font-mono text-[11px]">{{ $vid->created_at->format('d/m/Y') }}</td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center space-x-2">
                                    <button type="button" @click="openEditModal(@js($vid))" 
                                            class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded transition cursor-pointer" title="Edit Modul & Kuis">
                                        <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                    <form action="{{ route('admin.videos.destroy', $vid->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" 
                                                onclick="confirmDelete(this, {
                                                    title: 'Hapus Modul Video Edukasi',
                                                    message: 'Apakah Anda yakin ingin menghapus modul video edukasi ini beserta seluruh soal kuisnya? Data yang dihapus tidak dapat dipulihkan.',
                                                    itemDetail: '{{ addslashes($vid->title) }} ({{ $vid->category }})',
                                                    confirmText: 'Ya, Hapus Video'
                                                })"
                                                class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded transition cursor-pointer" title="Hapus Modul">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i data-lucide="video-off" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                                <p class="text-sm">Belum ada modul edukasi ditambahkan.</p>
                                <button type="button" @click="openCreateModal()" class="mt-2 text-xs text-lime-700 font-semibold hover:underline cursor-pointer">
                                    + Tambah modul pertama sekarang
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Video & Interactive Quiz Editor Modal -->
    <div x-show="showFormModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
         @keydown.escape.window="closeFormModal()">
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xl max-w-3xl w-full max-h-[92vh] flex flex-col overflow-hidden"
             @click.away="closeFormModal()">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between shrink-0 bg-slate-50/70">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-lime-100 text-lime-800 flex items-center justify-center font-bold text-sm">
                        <i data-lucide="film" class="w-4 h-4" x-show="!isEdit"></i>
                        <i data-lucide="edit-3" class="w-4 h-4" x-show="isEdit"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900" x-text="isEdit ? 'Edit Modul Video & Kuis Interaktif' : 'Tambah Modul Video & Kuis Edukasi'"></h3>
                        <p class="text-[11px] text-slate-500">Form visual terpadu untuk konten edukasi dan pertanyaan evaluasi mandiri.</p>
                    </div>
                </div>
                <button type="button" @click="closeFormModal()" class="text-slate-400 hover:text-slate-700 p-1 rounded-md transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-6 overflow-y-auto space-y-6 flex-1">

                <!-- Validation Error Alert -->
                <div x-show="validationError" x-cloak
                     class="p-3.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                        <span x-text="validationError" class="font-medium"></span>
                    </div>
                    <button type="button" @click="validationError = ''" class="text-rose-500 hover:text-rose-800 cursor-pointer">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </button>
                </div>

                <form id="videoFormElement" :action="formActionUrl" method="POST" @submit="handleSubmit($event)" class="space-y-6">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    <input type="hidden" name="quiz_data_raw" :value="serializeQuizzes()">

                    <!-- SECTION 1: Informasi Video Edukasi -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center space-x-2">
                                <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 inline-flex items-center justify-center text-[10px] font-bold">1</span>
                                <span>Informasi Video Edukasi</span>
                            </h4>
                            <span class="text-[10px] text-slate-400">* Wajib diisi</span>
                        </div>

                        <div>
                            <label class="block font-semibold uppercase tracking-wider text-[11px] text-slate-700 mb-1">Judul Video *</label>
                            <input type="text" name="title" x-model="formData.title" required
                                   placeholder="Contoh: Pentingnya Protein Hewani Cegah Stunting Sejak Dini"
                                   class="w-full px-3 py-2 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold uppercase tracking-wider text-[11px] text-slate-700 mb-1">Kategori Modul *</label>
                                <select name="category" x-model="formData.category" required
                                        class="w-full px-3 py-2 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                                    <option value="MPASI 6-23 Bln">MPASI 6-23 Bln</option>
                                    <option value="ASI Eksklusif 0-6 Bln">ASI Eksklusif 0-6 Bln</option>
                                    <option value="PHBS & Sanitasi">PHBS & Sanitasi</option>
                                    <option value="Gizi Ibu Hamil">Gizi Ibu Hamil</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold uppercase tracking-wider text-[11px] text-slate-700 mb-1">Tautan YouTube *</label>
                                <input type="url" name="youtube_url" x-model="formData.youtube_url" required
                                       placeholder="https://www.youtube.com/watch?v=..."
                                       class="w-full px-3 py-2 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold uppercase tracking-wider text-[11px] text-slate-700 mb-1">Intisari / Ringkasan Materi Edukasi *</label>
                            <textarea name="summary" x-model="formData.summary" rows="3" required
                                      placeholder="Poin-poin penting pembelajaran klinis untuk kader dan orang tua balita..."
                                      class="w-full px-3 py-2 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600 leading-relaxed"></textarea>
                        </div>
                    </div>

                    <!-- SECTION 2: Visual Quiz Builder (Best Practice) -->
                    <div class="space-y-4 pt-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-2">
                            <div class="flex items-center space-x-2">
                                <span class="w-5 h-5 rounded-full bg-lime-100 text-lime-800 inline-flex items-center justify-center text-[10px] font-bold">2</span>
                                <div>
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800">Kuis Evaluasi Pemahaman Mandiri</h4>
                                    <p class="text-[11px] text-slate-500">Soal pilihan ganda interaktif untuk menguji pemahaman penonton video (Opsional).</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-lime-50 text-lime-800 border border-lime-200"
                                      x-text="quizzes.length + ' Soal'"></span>
                                
                                <template x-if="quizzes.length > 0">
                                    <button type="button" @click="activeQuizView = (activeQuizView === 'editor' ? 'preview' : 'editor')"
                                            class="inline-flex items-center space-x-1 px-2.5 py-1 rounded text-[11px] font-semibold border transition cursor-pointer"
                                            :class="activeQuizView === 'preview' ? 'bg-purple-50 text-purple-800 border-purple-200' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200'">
                                        <i data-lucide="eye" class="w-3 h-3"></i>
                                        <span x-text="activeQuizView === 'preview' ? 'Kembali ke Editor' : 'Pratinjau Kuis'"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Quiz Editor View -->
                        <div x-show="activeQuizView === 'editor'" class="space-y-4">
                            
                            <!-- Empty State when 0 questions -->
                            <template x-if="quizzes.length === 0">
                                <div class="p-6 rounded-xl border border-dashed border-slate-300 bg-slate-50/50 text-center space-y-2">
                                    <div class="w-10 h-10 rounded-full bg-lime-100 text-lime-700 mx-auto flex items-center justify-center">
                                        <i data-lucide="help-circle" class="w-5 h-5"></i>
                                    </div>
                                    <h5 class="text-xs font-bold text-slate-800">Belum Ada Soal Kuis untuk Modul Ini</h5>
                                    <p class="text-[11px] text-slate-500 max-w-sm mx-auto">
                                        Tambahkan 2–3 soal pilihan ganda sederhana agar kader & orang tua dapat menguji pemahamannya secara mandiri.
                                    </p>
                                    <button type="button" @click="addQuestion()"
                                            class="mt-2 inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-md bg-lime-600 hover:bg-lime-700 text-white text-xs font-semibold shadow-2xs transition cursor-pointer">
                                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                        <span>Tambah Soal Pertama Sekarang</span>
                                    </button>
                                </div>
                            </template>

                            <!-- Question Cards List -->
                            <template x-for="(q, qIndex) in quizzes" :key="qIndex">
                                <div class="p-4 rounded-xl border border-slate-200 bg-white shadow-2xs space-y-3.5 transition hover:border-slate-300">
                                    
                                    <!-- Card Header Bar -->
                                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                                        <div class="flex items-center space-x-2">
                                            <span class="w-6 h-6 rounded-md bg-lime-600 text-white text-xs font-bold inline-flex items-center justify-center"
                                                  x-text="qIndex + 1"></span>
                                            <span class="text-xs font-bold text-slate-800" x-text="'Pertanyaan #' + (qIndex + 1)"></span>
                                            <template x-if="q.options && q.options.length > 0">
                                                <span class="text-[10px] text-slate-400 font-mono" x-text="'(' + q.options.length + ' Pilihan Jawaban)'"></span>
                                            </template>
                                        </div>

                                        <!-- Card Action Buttons -->
                                        <div class="flex items-center space-x-1">
                                            <!-- Move Up -->
                                            <button type="button" @click="moveQuestion(qIndex, -1)" :disabled="qIndex === 0"
                                                    class="p-1 rounded text-slate-400 hover:text-slate-700 disabled:opacity-30 disabled:hover:text-slate-400 cursor-pointer" title="Geser ke Atas">
                                                <i data-lucide="chevron-up" class="w-4 h-4"></i>
                                            </button>
                                            <!-- Move Down -->
                                            <button type="button" @click="moveQuestion(qIndex, 1)" :disabled="qIndex === quizzes.length - 1"
                                                    class="p-1 rounded text-slate-400 hover:text-slate-700 disabled:opacity-30 disabled:hover:text-slate-400 cursor-pointer" title="Geser ke Bawah">
                                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                            </button>
                                            <!-- Duplicate -->
                                            <button type="button" @click="duplicateQuestion(qIndex)"
                                                    class="p-1 rounded text-slate-400 hover:text-slate-700 cursor-pointer" title="Duplikasi Soal Ini">
                                                <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                            </button>
                                            <!-- Remove Question -->
                                            <button type="button" @click="removeQuestion(qIndex)"
                                                    class="p-1 rounded text-slate-400 hover:text-rose-600 cursor-pointer" title="Hapus Soal Ini">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Question Text Input -->
                                    <div>
                                        <label class="block font-semibold uppercase tracking-wider text-[10px] text-slate-600 mb-1">
                                            Teks Pertanyaan <span class="text-rose-500">*</span>
                                        </label>
                                        <textarea x-model="q.question" rows="2" required
                                                  placeholder="Contoh: Mengapa protein hewani lebih diutamakan daripada protein nabati dalam pencegahan stunting?"
                                                  class="w-full px-3 py-2 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600 leading-relaxed"></textarea>
                                    </div>

                                    <!-- Multiple Choice Options List -->
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <label class="block font-semibold uppercase tracking-wider text-[10px] text-slate-600">
                                                Pilihan Jawaban (Tandai Radio Lingkaran pada Jawaban Benar) <span class="text-rose-500">*</span>
                                            </label>
                                            <span class="text-[10px] text-slate-400">Minimal 2 pilihan, maksimal 5 pilihan</span>
                                        </div>

                                        <div class="space-y-2">
                                            <template x-for="(opt, oIndex) in q.options" :key="oIndex">
                                                <div class="flex items-center space-x-2 p-1.5 rounded-lg border transition"
                                                     :class="q.correct_index === oIndex ? 'bg-emerald-50/70 border-emerald-300 ring-1 ring-emerald-300' : 'bg-slate-50/60 border-slate-200'">
                                                    
                                                    <!-- Radio Selector for Correct Answer -->
                                                    <label class="flex items-center space-x-1.5 cursor-pointer pl-1.5 shrink-0" title="Klik untuk menjadikan opsi ini sebagai Kunci Jawaban Benar">
                                                        <input type="radio" :name="'correct_choice_' + qIndex" :value="oIndex"
                                                               :checked="q.correct_index === oIndex"
                                                               @change="q.correct_index = oIndex"
                                                               class="text-emerald-600 focus:ring-emerald-500">
                                                        <span class="w-5 h-5 rounded-md font-bold text-[11px] inline-flex items-center justify-center font-mono"
                                                              :class="q.correct_index === oIndex ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700'"
                                                              x-text="String.fromCharCode(65 + oIndex)"></span>
                                                    </label>

                                                    <!-- Option Text Input -->
                                                    <input type="text" x-model="q.options[oIndex]" required
                                                           :placeholder="'Tuliskan opsi ' + String.fromCharCode(65 + oIndex) + '...'"
                                                           class="flex-1 px-2.5 py-1 text-xs rounded border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">

                                                    <!-- Correct Answer Badge -->
                                                    <template x-if="q.correct_index === oIndex">
                                                        <span class="hidden sm:inline-flex items-center space-x-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 shrink-0">
                                                            <i data-lucide="check" class="w-3 h-3"></i>
                                                            <span>Kunci Jawaban</span>
                                                        </span>
                                                    </template>

                                                    <!-- Remove Option Button -->
                                                    <template x-if="q.options.length > 2">
                                                        <button type="button" @click="removeOption(qIndex, oIndex)"
                                                                class="p-1 rounded text-slate-400 hover:text-rose-600 cursor-pointer shrink-0" title="Hapus Pilihan Ini">
                                                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                                        </button>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Add Option Button -->
                                        <template x-if="q.options.length < 5">
                                            <button type="button" @click="addOption(qIndex)"
                                                    class="inline-flex items-center space-x-1 text-[11px] text-lime-700 font-semibold hover:text-lime-800 hover:underline pt-1 cursor-pointer">
                                                <i data-lucide="plus" class="w-3 h-3"></i>
                                                <span>+ Tambah Pilihan Jawaban (Opsi <span x-text="String.fromCharCode(65 + q.options.length)"></span>)</span>
                                            </button>
                                        </template>
                                    </div>

                                    <!-- Clinical Explanation / Pembahasan -->
                                    <div class="pt-2 border-t border-slate-100">
                                        <label class="block font-semibold uppercase tracking-wider text-[10px] text-slate-600 mb-1">
                                            Penjelasan Medis / Pembahasan Jawaban (Opsional)
                                        </label>
                                        <textarea x-model="q.explanation" rows="2"
                                                  placeholder="Jelaskan secara klinis mengapa jawaban tersebut benar untuk mengedukasi peserta kuis..."
                                                  class="w-full px-3 py-1.5 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600 leading-relaxed"></textarea>
                                        <p class="text-[10px] text-slate-400 mt-0.5">Penjelasan ini akan ditampilkan kepada pengguna setelah mereka menjawab kuis.</p>
                                    </div>

                                </div>
                            </template>

                            <!-- Add Question Action Button -->
                            <template x-if="quizzes.length > 0">
                                <div class="pt-2 flex items-center justify-between">
                                    <button type="button" @click="addQuestion()"
                                            class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-md border border-lime-600 bg-lime-50 hover:bg-lime-100 text-lime-800 text-xs font-semibold transition cursor-pointer">
                                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                        <span>+ Tambah Soal Kuis Baru</span>
                                    </button>

                                    <button type="button" @click="clearAllQuizzes()"
                                            class="text-[11px] text-slate-400 hover:text-rose-600 transition cursor-pointer">
                                        Kosongkan Semua Soal
                                    </button>
                                </div>
                            </template>

                        </div>

                        <!-- Quiz Interactive Live Preview View -->
                        <div x-show="activeQuizView === 'preview'" class="space-y-4">
                            <div class="p-4 rounded-xl bg-slate-900 text-white space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-lime-400"></span>
                                        <h5 class="text-xs font-bold uppercase tracking-wider text-lime-400">Simulasi Interaktif Tampilan Kuis</h5>
                                    </div>
                                    <span class="text-[11px] text-slate-400" x-text="quizzes.length + ' Pertanyaan'"></span>
                                </div>
                                <p class="text-[11px] text-slate-300 leading-relaxed">
                                    Ini adalah simulasi tampilan kuis yang akan dilihat oleh keluarga balita & kader saat membuka modul ini di Tumbuh Academy.
                                </p>
                            </div>

                            <div class="space-y-3.5">
                                <template x-for="(q, qIndex) in quizzes" :key="'prev_' + qIndex">
                                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/70 space-y-2.5 text-xs">
                                        <p class="font-bold text-slate-900">
                                            <span x-text="(qIndex + 1) + '. '"></span>
                                            <span x-text="q.question || '(Pertanyaan belum diisi)'"></span>
                                        </p>

                                        <div class="space-y-1.5 pl-2">
                                            <template x-for="(opt, oIndex) in q.options" :key="'prev_opt_' + oIndex">
                                                <div class="flex items-center space-x-2 p-2 rounded text-slate-700"
                                                     :class="oIndex === q.correct_index ? 'bg-emerald-100 text-emerald-900 font-semibold' : 'bg-white border border-slate-200'">
                                                    <span class="w-5 h-5 rounded-md font-mono text-[11px] font-bold inline-flex items-center justify-center"
                                                          :class="oIndex === q.correct_index ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600'"
                                                          x-text="String.fromCharCode(65 + oIndex)"></span>
                                                    <span x-text="opt || '(Opsi kosong)'"></span>
                                                    <template x-if="oIndex === q.correct_index">
                                                        <span class="ml-auto text-[10px] font-bold text-emerald-700 bg-emerald-200/60 px-1.5 py-0.5 rounded">✓ Kunci</span>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>

                                        <template x-if="q.explanation">
                                            <div class="mt-2 pt-2 border-t border-slate-200 text-[11px] text-slate-600 bg-white p-2.5 rounded-lg border">
                                                <strong class="text-slate-800">Pembahasan Klinis:</strong>
                                                <span x-text="q.explanation"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <div class="text-center pt-2">
                                <button type="button" @click="activeQuizView = 'editor'"
                                        class="px-4 py-2 rounded-md bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold cursor-pointer">
                                    &larr; Kembali ke Form Editor
                                </button>
                            </div>
                        </div>

                    </div>

                    <!-- Hidden Form Submit Trigger -->
                    <button type="submit" id="hiddenSubmitBtn" class="hidden"></button>
                </form>

            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3.5 border-t border-slate-200 bg-slate-50 flex items-center justify-between shrink-0">
                <span class="text-[11px] text-slate-500 font-mono" x-text="quizzes.length + ' Soal Kuis Terpasang'"></span>
                <div class="flex items-center space-x-2">
                    <button type="button" @click="closeFormModal()"
                            class="px-4 py-2 rounded-md border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="submitForm()"
                            class="inline-flex items-center space-x-1.5 px-5 py-2 rounded-md bg-lime-600 hover:bg-lime-700 text-white text-xs font-semibold shadow-sm transition cursor-pointer">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span x-text="isEdit ? 'Simpan Perubahan Modul' : 'Simpan Modul & Kuis'"></span>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- Quick Quiz Detail Modal (Table Read-Only Preview) -->
    <div x-show="showQuickQuizModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
         @keydown.escape.window="showQuickQuizModal = false">
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xl max-w-xl w-full p-6 space-y-4"
             @click.away="showQuickQuizModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2.5">
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs">
                        <i data-lucide="help-circle" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Daftar Pertanyaan Kuis</h3>
                        <p class="text-[11px] text-slate-500 truncate max-w-sm" x-text="quickQuizVideo ? quickQuizVideo.title : ''"></p>
                    </div>
                </div>
                <button type="button" @click="showQuickQuizModal = false" class="text-slate-400 hover:text-slate-700 cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Quiz Questions Preview -->
            <div class="max-h-[60vh] overflow-y-auto space-y-3 text-xs pr-1">
                <template x-if="quickQuizVideo && quickQuizVideo.quiz_data">
                    <div class="space-y-3">
                        <template x-for="(q, qIndex) in quickQuizVideo.quiz_data" :key="'quick_' + qIndex">
                            <div class="p-3.5 rounded-lg border border-slate-200 bg-slate-50/70 space-y-2">
                                <p class="font-bold text-slate-900">
                                    <span x-text="(qIndex + 1) + '. '"></span>
                                    <span x-text="q.question"></span>
                                </p>
                                <div class="space-y-1 pl-2">
                                    <template x-for="(opt, oIndex) in q.options" :key="'quick_opt_' + oIndex">
                                        <div class="p-1.5 rounded flex items-center space-x-2"
                                             :class="oIndex === q.correct_index ? 'bg-emerald-100 text-emerald-900 font-semibold' : 'text-slate-600'">
                                            <span class="font-mono font-bold" x-text="String.fromCharCode(65 + oIndex) + '.'"></span>
                                            <span x-text="opt"></span>
                                            <template x-if="oIndex === q.correct_index">
                                                <span class="ml-auto text-[10px] font-bold text-emerald-700 bg-emerald-200 px-1.5 py-0.2 rounded">✓ Kunci</span>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                                <template x-if="q.explanation">
                                    <div class="mt-2 pt-1.5 border-t border-slate-200 text-[11px] text-slate-600">
                                        <strong>Pembahasan:</strong> <span x-text="q.explanation"></span>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                <button type="button" @click="showQuickQuizModal = false; openEditModal(quickQuizVideo)"
                        class="text-xs text-lime-700 font-semibold hover:underline flex items-center space-x-1 cursor-pointer">
                    <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                    <span>Edit Soal Kuis Ini</span>
                </button>
                <button type="button" @click="showQuickQuizModal = false"
                        class="px-3.5 py-1.5 rounded-md border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function videoManager() {
        return {
            showFormModal: false,
            isEdit: false,
            formActionUrl: '{{ route("admin.videos.store") }}',
            activeQuizView: 'editor',
            validationError: '',
            showQuickQuizModal: false,
            quickQuizVideo: null,

            formData: {
                id: null,
                title: '',
                category: 'MPASI 6-23 Bln',
                youtube_url: '',
                summary: ''
            },

            quizzes: [],

            openCreateModal() {
                this.isEdit = false;
                this.formActionUrl = '{{ route("admin.videos.store") }}';
                this.formData = {
                    id: null,
                    title: '',
                    category: 'MPASI 6-23 Bln',
                    youtube_url: '',
                    summary: ''
                };
                this.quizzes = [
                    {
                        question: '',
                        options: ['', '', '', ''],
                        correct_index: 0,
                        explanation: ''
                    }
                ];
                this.activeQuizView = 'editor';
                this.validationError = '';
                this.showFormModal = true;
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            },

            openEditModal(video) {
                this.isEdit = true;
                this.formActionUrl = '{{ url("admin/videos") }}/' + video.id;
                this.formData = {
                    id: video.id,
                    title: video.title,
                    category: video.category,
                    youtube_url: video.youtube_url,
                    summary: video.summary
                };

                // Deep copy quiz data into visual builder state
                if (video.quiz_data && Array.isArray(video.quiz_data) && video.quiz_data.length > 0) {
                    this.quizzes = video.quiz_data.map(q => ({
                        question: q.question || '',
                        options: Array.isArray(q.options) && q.options.length >= 2 
                            ? [...q.options] 
                            : ['', '', '', ''],
                        correct_index: typeof q.correct_index === 'number' ? q.correct_index : 0,
                        explanation: q.explanation || ''
                    }));
                } else {
                    this.quizzes = [];
                }

                this.activeQuizView = 'editor';
                this.validationError = '';
                this.showFormModal = true;
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            },

            closeFormModal() {
                this.showFormModal = false;
            },

            openQuickQuizPreview(video) {
                this.quickQuizVideo = video;
                this.showQuickQuizModal = true;
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            },

            addQuestion() {
                this.quizzes.push({
                    question: '',
                    options: ['', '', '', ''],
                    correct_index: 0,
                    explanation: ''
                });
                this.activeQuizView = 'editor';
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            },

            removeQuestion(index) {
                const q = this.quizzes[index];
                if (q && q.question.trim() !== '') {
                    confirmDelete(null, {
                        title: 'Hapus Soal Kuis #' + (index + 1),
                        message: 'Apakah Anda yakin ingin menghapus soal ini dari modul?',
                        itemDetail: q.question,
                        confirmText: 'Ya, Hapus Soal',
                        onConfirm: () => {
                            this.quizzes.splice(index, 1);
                            this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                        }
                    });
                } else {
                    this.quizzes.splice(index, 1);
                    this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                }
            },

            duplicateQuestion(index) {
                const target = this.quizzes[index];
                if (!target) return;
                const cloned = JSON.parse(JSON.stringify(target));
                this.quizzes.splice(index + 1, 0, cloned);
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            },

            moveQuestion(index, dir) {
                const newIndex = index + dir;
                if (newIndex < 0 || newIndex >= this.quizzes.length) return;
                const temp = this.quizzes[index];
                this.quizzes[index] = this.quizzes[newIndex];
                this.quizzes[newIndex] = temp;
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            },

            addOption(qIndex) {
                if (this.quizzes[qIndex].options.length < 5) {
                    this.quizzes[qIndex].options.push('');
                    this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                }
            },

            removeOption(qIndex, oIndex) {
                const opts = this.quizzes[qIndex].options;
                if (opts.length > 2) {
                    opts.splice(oIndex, 1);
                    if (this.quizzes[qIndex].correct_index >= opts.length) {
                        this.quizzes[qIndex].correct_index = 0;
                    }
                    this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                }
            },

            clearAllQuizzes() {
                confirmDelete(null, {
                    title: 'Kosongkan Semua Soal Kuis',
                    message: 'Apakah Anda yakin ingin menghapus seluruh soal kuis untuk video ini?',
                    confirmText: 'Ya, Kosongkan',
                    onConfirm: () => {
                        this.quizzes = [];
                        this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                    }
                });
            },

            serializeQuizzes() {
                const valid = [];
                for (const q of this.quizzes) {
                    const qText = (q.question || '').trim();
                    const filteredOpts = (q.options || [])
                        .map(opt => (opt || '').trim())
                        .filter(opt => opt !== '');

                    if (qText !== '' && filteredOpts.length >= 2) {
                        let cIndex = typeof q.correct_index === 'number' ? q.correct_index : 0;
                        if (cIndex < 0 || cIndex >= filteredOpts.length) {
                            cIndex = 0;
                        }
                        valid.push({
                            question: qText,
                            options: filteredOpts,
                            correct_index: cIndex,
                            explanation: (q.explanation || '').trim()
                        });
                    }
                }
                return valid.length > 0 ? JSON.stringify(valid) : '';
            },

            submitForm() {
                // Trigger HTML5 validation first
                const form = document.getElementById('videoFormElement');
                if (!form.reportValidity()) {
                    return;
                }

                // Custom validation for quizzes
                for (let i = 0; i < this.quizzes.length; i++) {
                    const q = this.quizzes[i];
                    const qText = (q.question || '').trim();
                    const filledOpts = (q.options || []).filter(o => (o || '').trim() !== '');

                    // If question is completely empty and there are other questions, we can ignore or alert
                    if (qText === '' && filledOpts.length === 0) {
                        continue;
                    }

                    if (qText === '') {
                        this.validationError = 'Soal #' + (i + 1) + ' belum memiliki teks pertanyaan.';
                        this.activeQuizView = 'editor';
                        return;
                    }

                    if (filledOpts.length < 2) {
                        this.validationError = 'Soal #' + (i + 1) + ' harus memiliki minimal 2 pilihan jawaban yang terisi.';
                        this.activeQuizView = 'editor';
                        return;
                    }
                }

                this.validationError = '';
                form.submit();
            },

            handleSubmit(e) {
                // Handled via submitForm
            }
        };
    }
</script>
@endpush
@endsection
