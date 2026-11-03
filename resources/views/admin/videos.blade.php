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
                Kelola materi literasi gizi, video edukasi YouTube, dan kuis pemahaman mandiri untuk kader dan keluarga balita.
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

    <!-- Search & Filter Card -->
    <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm space-y-3">
        <form method="GET" action="{{ route('admin.videos') }}" class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <!-- Search Keyword Input with Cari Button -->
            <div class="flex items-center flex-1 min-w-[220px]">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Cari judul modul, ringkasan, atau materi..."
                           class="w-full pl-9 pr-8 py-2 text-xs rounded-l-lg border border-r-0 border-slate-300 bg-white text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600 transition">
                    @if($search)
                        <a href="{{ route('admin.videos', array_merge(request()->except('search'), ['search' => ''])) }}"
                           class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600" title="Hapus kata kunci">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </a>
                    @endif
                </div>
                <button type="submit"
                        class="px-3.5 py-2 rounded-r-lg bg-lime-600 hover:bg-lime-700 text-white text-xs font-semibold border border-lime-600 shadow-2xs transition cursor-pointer shrink-0 flex items-center space-x-1">
                    <span>Cari</span>
                </button>
            </div>

            <!-- Filters Group -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Category Select -->
                <div class="w-44">
                    <select name="category" onchange="this.form.submit()"
                            class="w-full px-2.5 py-2 text-xs rounded-lg border border-slate-300 bg-white text-slate-700 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600 transition cursor-pointer">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ $selectedCategory === $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Quiz Status Filter -->
                <div class="w-40">
                    <select name="quiz_status" onchange="this.form.submit()"
                            class="w-full px-2.5 py-2 text-xs rounded-lg border border-slate-300 bg-white text-slate-700 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600 transition cursor-pointer">
                        <option value="all" {{ $quizStatus === 'all' ? 'selected' : '' }}>Semua Kuis</option>
                        <option value="has_quiz" {{ $quizStatus === 'has_quiz' ? 'selected' : '' }}>Ada Kuis ({{ $quizCount }})</option>
                        <option value="no_quiz" {{ $quizStatus === 'no_quiz' ? 'selected' : '' }}>Belum Ada Kuis ({{ $noQuizCount }})</option>
                    </select>
                </div>
            </div>
        </form>

        <!-- Stats & Active Filter Info Bar -->
        <div class="flex items-center justify-between pt-2.5 border-t border-slate-100 text-[11px] text-slate-500">
            <div class="flex items-center space-x-2">
                <span>Menampilkan <strong class="text-slate-800">{{ $videos->total() }}</strong> dari <strong class="text-slate-800">{{ $totalCount }}</strong> modul edukasi</span>
                @if($search || ($selectedCategory && $selectedCategory !== 'Semua Kategori') || ($quizStatus && $quizStatus !== 'all'))
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        Filter Aktif
                    </span>
                @endif
            </div>
            <div class="hidden sm:flex items-center space-x-3 text-[11px]">
                <span class="flex items-center space-x-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Dengan Kuis: <strong class="text-slate-700">{{ $quizCount }}</strong></span>
                </span>
                <span class="flex items-center space-x-1">
                    <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                    <span>Tanpa Kuis: <strong class="text-slate-700">{{ $noQuizCount }}</strong></span>
                </span>
            </div>
        </div>
    </div>

    <!-- Video Modules Table (Optimized 1-Screen Width, No Horizontal Scroll) -->
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs table-fixed">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-3.5 w-[42%]">Judul Modul &amp; Ringkasan</th>
                        <th class="py-3 px-2.5 w-[16%]">Kategori</th>
                        <th class="py-3 px-2.5 w-[10%]">YouTube</th>
                        <th class="py-3 px-2.5 w-[13%] text-center">Kuis Pemahaman</th>
                        <th class="py-3 px-2.5 w-[10%]">Tanggal</th>
                        <th class="py-3 px-3.5 w-[9%] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($videos as $vid)
                        @php
                            $quizCount = !empty($vid->quiz_data) && is_array($vid->quiz_data) ? count($vid->quiz_data) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-2.5 px-3.5 font-semibold text-slate-900">
                                <div class="flex items-center space-x-2.5 min-w-0">
                                    <div class="relative w-12 h-8 sm:w-14 sm:h-9 rounded overflow-hidden bg-slate-100 shrink-0 border border-slate-200 shadow-2xs">
                                        <img src="{{ $vid->youtube_thumbnail_url }}" alt="" class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('edukasi.show', $vid->id) }}" target="_blank"
                                           class="hover:text-lime-700 transition flex items-center space-x-1 group"
                                           title="{{ $vid->title }}">
                                            <span class="truncate block font-semibold text-slate-900 group-hover:text-lime-700">{{ Str::limit($vid->title, 42) }}</span>
                                            <i data-lucide="external-link" class="w-3 h-3 text-slate-400 shrink-0 opacity-0 group-hover:opacity-100 transition"></i>
                                        </a>
                                        <p class="text-[11px] font-normal text-slate-500 truncate mt-0.5" title="{{ $vid->summary }}">
                                            {{ Str::limit($vid->summary, 48) }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-2.5 px-2.5">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-lime-50 text-lime-800 border border-lime-200 truncate max-w-full"
                                      title="{{ $vid->category }}">
                                    {{ $vid->category }}
                                </span>
                            </td>
                            <td class="py-2.5 px-2.5 text-slate-500 text-[11px] whitespace-nowrap">
                                <a href="{{ $vid->youtube_url }}" target="_blank" class="text-lime-700 hover:underline inline-flex items-center space-x-1">
                                    <i data-lucide="youtube" class="w-3.5 h-3.5 text-rose-600 shrink-0"></i>
                                    <span>Tonton</span>
                                </a>
                            </td>
                            <td class="py-2.5 px-2.5 text-center whitespace-nowrap">
                                @if($quizCount > 0)
                                    <button type="button" @click="openQuickQuizPreview(@js($vid))"
                                            class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition cursor-pointer"
                                            title="Klik untuk melihat pratinjau soal kuis">
                                        <i data-lucide="help-circle" class="w-3 h-3 shrink-0"></i>
                                        <span>{{ $quizCount }} Soal</span>
                                    </button>
                                @else
                                    <span class="text-slate-400 text-[10px] sm:text-[11px] italic">Tidak ada kuis</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-2.5 text-slate-400 font-mono text-[11px] whitespace-nowrap">
                                {{ $vid->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-2.5 px-3.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center space-x-1.5 justify-end">
                                    <button type="button" @click="openEditModal(@js($vid))" 
                                            class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded transition cursor-pointer" title="Edit Modul &amp; Kuis">
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
                                @if($search || ($selectedCategory && $selectedCategory !== 'Semua Kategori') || ($quizStatus && $quizStatus !== 'all'))
                                    <i data-lucide="search-x" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                                    <p class="text-sm font-semibold text-slate-700">Tidak ada modul video yang sesuai filter.</p>
                                    <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau ubah filter kategori &amp; kuis.</p>
                                    <a href="{{ route('admin.videos') }}" class="inline-flex items-center space-x-1 mt-3 px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition">
                                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                        <span>Reset Semua Filter</span>
                                    </a>
                                @else
                                    <i data-lucide="video-off" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                                    <p class="text-sm font-semibold text-slate-700">Belum ada modul edukasi ditambahkan.</p>
                                    <button type="button" @click="openCreateModal()" class="mt-2 text-xs text-lime-700 font-semibold hover:underline cursor-pointer">
                                        + Tambah modul pertama sekarang
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($videos->hasPages())
            <div class="px-5 py-3 border-t border-slate-200 bg-slate-50/50">
                {{ $videos->links() }}
            </div>
        @endif
    </div>

    <!-- Video & Interactive Quiz Editor Modal -->
    <template x-teleport="body">
        <div x-show="showFormModal" x-cloak
             class="fixed inset-0 z-[60] overflow-y-auto bg-slate-900/60 backdrop-blur-sm transform-gpu flex items-center justify-center p-3 sm:p-5"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="closeFormModal()"
             @click.self="closeFormModal()">

            <div class="relative bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-3xl w-full max-h-[85vh] flex flex-col overflow-hidden"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                 @click.stop>
            
                    <!-- Modal Header -->
                    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between shrink-0 bg-white">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-lime-100 text-lime-800 flex items-center justify-center font-bold text-sm shadow-2xs shrink-0">
                                <svg class="w-4 h-4 shrink-0" x-show="!isEdit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="3" rx="2"/>
                                    <path d="M7 3v18M17 3v18M3 7.5h4M3 12h18M3 16.5h4M17 7.5h4M17 16.5h4"/>
                                </svg>
                                <svg class="w-4 h-4 shrink-0" x-show="isEdit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 tracking-tight" x-text="isEdit ? 'Edit Modul Video Edukasi' : 'Tambah Modul Video Edukasi'"></h3>
                                <p class="text-[11px] text-slate-500">Kelola konten video YouTube posyandu dan evaluasi kuis mandiri.</p>
                            </div>
                        </div>
                        <button type="button" @click="closeFormModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer" title="Tutup">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6 6 18M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Segmented Navigation Bar -->
                    <div class="px-6 py-2.5 bg-slate-50 border-b border-slate-200 shrink-0 flex items-center justify-between gap-3">
                        <div class="inline-flex p-1 bg-slate-200/70 rounded-xl space-x-1">
                            <button type="button" 
                                    @click="activeModalTab = 'video'"
                                    class="py-1.5 px-3.5 rounded-lg text-xs font-semibold flex items-center space-x-2 transition cursor-pointer"
                                    :class="activeModalTab === 'video' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900'">
                                <svg class="w-3.5 h-3.5 shrink-0" :class="activeModalTab === 'video' ? 'text-lime-600' : 'text-slate-400'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m22 8-6 4 6 4V8ZM2 6h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2Z"/>
                                </svg>
                                <span>1. Konten Video</span>
                            </button>
                            <button type="button" 
                                    @click="activeModalTab = 'quiz'"
                                    class="py-1.5 px-3.5 rounded-lg text-xs font-semibold flex items-center space-x-2 transition cursor-pointer"
                                    :class="activeModalTab === 'quiz' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900'">
                                <svg class="w-3.5 h-3.5 shrink-0" :class="activeModalTab === 'quiz' ? 'text-lime-600' : 'text-slate-400'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3M12 17h.01"/>
                                </svg>
                                <span>2. Kuis Pemahaman</span>
                                <span class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] font-bold"
                                      :class="quizzes.length > 0 ? (activeModalTab === 'quiz' ? 'bg-lime-100 text-lime-800' : 'bg-lime-200/80 text-lime-900') : 'bg-slate-300 text-slate-600'"
                                      x-text="quizzes.length"></span>
                            </button>
                        </div>
                        <div class="hidden sm:block text-[11px] text-slate-400">
                            <span x-show="activeModalTab === 'video'">Informasi utama materi video</span>
                            <span x-show="activeModalTab === 'quiz'">Pertanyaan evaluasi mandiri (opsional)</span>
                        </div>
                    </div>

                    <!-- Modal Body (Scrollable) -->
                    <div class="p-6 overflow-y-auto space-y-5 flex-1">

                        <!-- Validation Error Alert -->
                        <div x-show="validationError" x-cloak
                             class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center justify-between">
                            <div class="flex items-center space-x-2.5">
                                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                                <span x-text="validationError" class="font-medium"></span>
                            </div>
                            <button type="button" @click="validationError = ''" class="text-rose-500 hover:text-rose-800 p-1 rounded cursor-pointer">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>

                        <form id="videoFormElement" :action="formActionUrl" method="POST" @submit="handleSubmit($event)" class="space-y-5">
                            @csrf
                            <template x-if="isEdit">
                                <input type="hidden" name="_method" value="PUT">
                            </template>
                            <input type="hidden" name="quiz_data_raw" :value="serializeQuizzes()">

                            <!-- TAB 1: INFORMASI VIDEO EDUKASI -->
                            <div x-show="activeModalTab === 'video'" class="space-y-4">
                                
                                <!-- Judul Video -->
                                <div>
                                    <label class="block font-semibold uppercase tracking-wider text-[11px] text-slate-700 mb-1.5">
                                        Judul Modul Video <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="title" x-model="formData.title" required
                                           placeholder="Contoh: Pentingnya Protein Hewani Cegah Stunting Sejak Dini"
                                           class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600 placeholder:text-slate-400">
                                </div>

                                <!-- 2 Kolom: Kategori & Tautan YouTube -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block font-semibold uppercase tracking-wider text-[11px] text-slate-700 mb-1.5">
                                            Kategori Pembelajaran <span class="text-rose-500">*</span>
                                        </label>
                                        <select name="category" x-model="formData.category" required
                                                class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                                            <option value="MPASI 6-23 Bln">MPASI 6-23 Bln</option>
                                            <option value="ASI Eksklusif 0-6 Bln">ASI Eksklusif 0-6 Bln</option>
                                            <option value="PHBS & Sanitasi">PHBS & Sanitasi</option>
                                            <option value="Gizi Ibu Hamil">Gizi Ibu Hamil</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block font-semibold uppercase tracking-wider text-[11px] text-slate-700 mb-1.5">
                                            Tautan YouTube <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="url" name="youtube_url" x-model="formData.youtube_url" required
                                               placeholder="https://www.youtube.com/watch?v=..."
                                               class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600 placeholder:text-slate-400">
                                    </div>
                                </div>

                                <!-- YouTube Live Thumbnail Preview Card -->
                                <template x-if="getYoutubeVideoId(formData.youtube_url)">
                                    <div class="p-3 rounded-xl border border-emerald-200 bg-emerald-50/50 flex items-center space-x-3.5">
                                        <img :src="'https://img.youtube.com/vi/' + getYoutubeVideoId(formData.youtube_url) + '/mqdefault.jpg'"
                                             alt="Thumbnail YouTube"
                                             class="w-24 h-14 object-cover rounded-lg border border-slate-200 shadow-2xs shrink-0">
                                        <div class="min-w-0 flex-1">
                                            <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                <svg class="w-3 h-3 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <path d="m9 12 2 2 4-4"/>
                                                </svg>
                                                <span>Video YouTube Terverifikasi</span>
                                            </span>
                                            <p class="text-xs font-semibold text-slate-800 truncate mt-1" x-text="formData.title || 'Judul modul akan tampil di sini'"></p>
                                            <p class="text-[11px] text-slate-500 truncate mt-0.5 font-mono" x-text="formData.youtube_url"></p>
                                        </div>
                                    </div>
                                </template>

                                <!-- Ringkasan Materi -->
                                <div>
                                    <label class="block font-semibold uppercase tracking-wider text-[11px] text-slate-700 mb-1.5">
                                        Intisari &amp; Ringkasan Materi <span class="text-rose-500">*</span>
                                    </label>
                                    <textarea name="summary" x-model="formData.summary" rows="3" required
                                              placeholder="Tuliskan poin-poin utama pembelajaran gizi yang disampaikan dalam video untuk panduan kader dan orang tua..."
                                              class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600 leading-relaxed placeholder:text-slate-400"></textarea>
                                    <p class="text-[11px] text-slate-400 mt-1">Ringkasan ini akan tampil pada katalog edukasi Tumbuh Academy.</p>
                                </div>

                                <!-- Lanjut ke Tab Kuis Card -->
                                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/70 flex items-center justify-between gap-3">
                                    <div class="flex items-center space-x-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-lime-100 text-lime-800 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"/>
                                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3M12 17h.01"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-800">Kuis Evaluasi Pemahaman Mandiri</p>
                                            <p class="text-[11px] text-slate-500" x-text="quizzes.length > 0 ? (quizzes.length + ' soal telah dibuat untuk video ini.') : 'Opsional: Uji pemahaman materi dengan soal pilihan ganda.'"></p>
                                        </div>
                                    </div>
                                    <button type="button" @click="activeModalTab = 'quiz'"
                                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-800 text-xs font-semibold shadow-2xs transition cursor-pointer shrink-0">
                                        <span>Kelola Kuis</span>
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m9 18 6-6-6-6"/>
                                        </svg>
                                    </button>
                                </div>

                            </div>

                            <!-- TAB 2: KUIS EVALUASI PEMAHAMAN MANDIRI -->
                            <div x-show="activeModalTab === 'quiz'" class="space-y-4">
                                
                                <!-- Toolbar Tab 2 -->
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-slate-100 pb-3">
                                    <div>
                                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900">Pertanyaan Kuis Interaktif</h4>
                                        <p class="text-[11px] text-slate-500">Soal pilihan ganda otomatis dievaluasi saat ditonton di katalog Academy.</p>
                                    </div>
                                    <div class="flex items-center space-x-1.5 shrink-0">
                                        <template x-if="quizzes.length > 0">
                                            <button type="button" @click="activeQuizView = (activeQuizView === 'editor' ? 'preview' : 'editor')"
                                                    class="inline-flex items-center gap-1.5 pl-2 pr-2.5 py-1 rounded-md text-xs font-semibold border transition cursor-pointer shadow-2xs"
                                                    :class="activeQuizView === 'preview' ? 'bg-purple-50 text-purple-800 border-purple-300' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50 hover:border-slate-400'">
                                                <!-- Eye icon when in editor -->
                                                <svg x-show="activeQuizView !== 'preview'" class="w-3.5 h-3.5 text-slate-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                                <!-- Edit icon when in preview -->
                                                <svg x-show="activeQuizView === 'preview'" class="w-3.5 h-3.5 text-purple-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M12 20h9"/>
                                                    <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                                </svg>
                                                <span x-text="activeQuizView === 'preview' ? 'Editor' : 'Pratinjau'"></span>
                                            </button>
                                        </template>

                                        <button type="button" @click="addQuestion()"
                                                class="inline-flex items-center gap-1.5 pl-2 pr-2.5 py-1 rounded-md bg-lime-600 hover:bg-lime-700 text-white text-xs font-semibold shadow-2xs transition cursor-pointer">
                                            <svg class="w-3.5 h-3.5 text-white shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 12h14"/>
                                                <path d="M12 5v14"/>
                                            </svg>
                                            <span>Tambah</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Editor View -->
                                <div x-show="activeQuizView === 'editor'" class="space-y-4">
                                    
                                    <!-- Empty State -->
                                    <template x-if="quizzes.length === 0">
                                        <div class="py-10 px-6 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 text-center space-y-3">
                                            <div class="w-12 h-12 rounded-2xl bg-lime-100 text-lime-700 mx-auto flex items-center justify-center shadow-2xs">
                                                <svg class="w-6 h-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                                                    <path d="M12 17h.01"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h5 class="text-xs font-bold text-slate-900">Belum Ada Soal Kuis</h5>
                                                <p class="text-[11px] text-slate-500 max-w-sm mx-auto mt-1 leading-relaxed">
                                                    Kuis bersifat opsional. Anda dapat langsung menyimpan modul tanpa kuis, atau tambahkan 2–3 pertanyaan pilihan ganda untuk menguji pemahaman penonton.
                                                </p>
                                            </div>
                                            <button type="button" @click="addQuestion()"
                                                    class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-lime-600 hover:bg-lime-700 text-white text-xs font-semibold shadow-2xs transition cursor-pointer">
                                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M5 12h14"/>
                                                    <path d="M12 5v14"/>
                                                </svg>
                                                <span>Buat Pertanyaan Pertama</span>
                                            </button>
                                        </div>
                                    </template>

                                    <!-- Question Cards List -->
                                    <template x-for="(q, qIndex) in quizzes" :key="qIndex">
                                        <div class="rounded-xl border border-slate-200 bg-white shadow-2xs overflow-hidden transition hover:border-slate-300">
                                            
                                            <!-- Card Header Bar -->
                                            <div class="bg-slate-50/90 px-4 py-2.5 border-b border-slate-200 flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    <span class="w-6 h-6 rounded-md bg-lime-600 text-white text-xs font-bold inline-flex items-center justify-center font-mono shadow-2xs"
                                                          x-text="qIndex + 1"></span>
                                                    <span class="text-xs font-bold text-slate-800" x-text="'Pertanyaan #' + (qIndex + 1)"></span>
                                                    <span class="text-[10px] text-slate-400 font-mono" x-text="'(' + (q.options ? q.options.length : 0) + ' Pilihan)'"></span>
                                                </div>

                                                <!-- Actions -->
                                                <div class="flex items-center space-x-0.5">
                                                    <button type="button" @click="moveQuestion(qIndex, -1)" :disabled="qIndex === 0"
                                                            class="p-1.5 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 disabled:opacity-20 cursor-pointer" title="Geser ke Atas">
                                                        <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="m18 15-6-6-6 6"/>
                                                        </svg>
                                                    </button>
                                                    <button type="button" @click="moveQuestion(qIndex, 1)" :disabled="qIndex === quizzes.length - 1"
                                                            class="p-1.5 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 disabled:opacity-20 cursor-pointer" title="Geser ke Bawah">
                                                        <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="m6 9 6 6 6-6"/>
                                                        </svg>
                                                    </button>
                                                    <button type="button" @click="duplicateQuestion(qIndex)"
                                                            class="p-1.5 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 cursor-pointer" title="Duplikasi Soal Ini">
                                                        <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/>
                                                            <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                                                        </svg>
                                                    </button>
                                                    <button type="button" @click="removeQuestion(qIndex)"
                                                            class="p-1.5 rounded-md text-slate-400 hover:text-rose-600 hover:bg-rose-50 cursor-pointer" title="Hapus Soal Ini">
                                                        <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M3 6h18"/>
                                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Card Content -->
                                            <div class="p-4 space-y-3.5">
                                                <!-- Question Text -->
                                                <div>
                                                    <label class="block font-semibold uppercase tracking-wider text-[10px] text-slate-600 mb-1">
                                                        Teks Pertanyaan <span class="text-rose-500">*</span>
                                                    </label>
                                                    <textarea x-model="q.question" rows="2" required
                                                              placeholder="Tuliskan pertanyaan evaluasi yang relevan dengan isi video..."
                                                              class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600 leading-relaxed placeholder:text-slate-400"></textarea>
                                                </div>

                                                <!-- Options -->
                                                <div class="space-y-2">
                                                    <div class="flex items-center justify-between">
                                                        <label class="block font-semibold uppercase tracking-wider text-[10px] text-slate-600">
                                                            Pilihan Jawaban (Tandai Jawaban Benar) <span class="text-rose-500">*</span>
                                                        </label>
                                                        <span class="text-[10px] text-slate-400">Minimal 2, maksimal 5 opsi</span>
                                                    </div>

                                                    <div class="space-y-1.5">
                                                        <template x-for="(opt, oIndex) in q.options" :key="oIndex">
                                                            <div class="flex items-center space-x-2 p-1.5 rounded-lg border transition"
                                                                 :class="q.correct_index === oIndex ? 'bg-emerald-50/80 border-emerald-300 ring-1 ring-emerald-300' : 'bg-slate-50/50 border-slate-200'">
                                                                
                                                                <label class="flex items-center space-x-1.5 cursor-pointer pl-1 shrink-0" title="Klik untuk jadikan Kunci Jawaban">
                                                                    <input type="radio" :name="'correct_choice_' + qIndex" :value="oIndex"
                                                                           :checked="q.correct_index === oIndex"
                                                                           @change="q.correct_index = oIndex"
                                                                           class="text-emerald-600 focus:ring-emerald-500">
                                                                    <span class="w-5 h-5 rounded-md font-bold text-[11px] inline-flex items-center justify-center font-mono"
                                                                          :class="q.correct_index === oIndex ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-slate-200 text-slate-700'"
                                                                          x-text="String.fromCharCode(65 + oIndex)"></span>
                                                                </label>

                                                                <input type="text" x-model="q.options[oIndex]" required
                                                                       :placeholder="'Tuliskan opsi ' + String.fromCharCode(65 + oIndex) + '...'"
                                                                       class="flex-1 px-2.5 py-1 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 placeholder:text-slate-400">

                                                                <template x-if="q.correct_index === oIndex">
                                                                    <span class="hidden sm:inline-flex items-center space-x-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 shrink-0">
                                                                        <svg class="w-3 h-3 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                                            <path d="M20 6 9 17l-5-5"/>
                                                                        </svg>
                                                                        <span>Kunci</span>
                                                                    </span>
                                                                </template>

                                                                <template x-if="q.options.length > 2">
                                                                    <button type="button" @click="removeOption(qIndex, oIndex)"
                                                                            class="p-1 rounded text-slate-400 hover:text-rose-600 cursor-pointer shrink-0" title="Hapus Opsi">
                                                                        <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                            <path d="M18 6 6 18"/>
                                                                            <path d="m6 6 12 12"/>
                                                                        </svg>
                                                                    </button>
                                                                </template>
                                                            </div>
                                                        </template>
                                                    </div>

                                                    <template x-if="q.options.length < 5">
                                                        <button type="button" @click="addOption(qIndex)"
                                                                class="inline-flex items-center space-x-1 text-[11px] text-lime-700 font-semibold hover:text-lime-800 hover:underline pt-0.5 cursor-pointer">
                                                            <svg class="w-3 h-3 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M5 12h14"/>
                                                                <path d="M12 5v14"/>
                                                            </svg>
                                                            <span>+ Tambah Pilihan (Opsi <span x-text="String.fromCharCode(65 + q.options.length)"></span>)</span>
                                                        </button>
                                                    </template>
                                                </div>

                                                <!-- Pembahasan / Penjelasan Medis -->
                                                <div class="pt-2 border-t border-slate-100">
                                                    <label class="block font-semibold uppercase tracking-wider text-[10px] text-slate-600 mb-1">
                                                        Penjelasan Medis / Pembahasan Jawaban (Opsional)
                                                    </label>
                                                    <textarea x-model="q.explanation" rows="2"
                                                              placeholder="Jelaskan secara klinis mengapa jawaban tersebut benar untuk mengedukasi peserta..."
                                                              class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600 leading-relaxed placeholder:text-slate-400"></textarea>
                                                </div>

                                            </div>
                                        </div>
                                    </template>

                                    <!-- Add Question Bottom Toolbar -->
                                    <template x-if="quizzes.length > 0">
                                        <div class="pt-2.5 flex items-center justify-between border-t border-slate-100">
                                            <button type="button" @click="addQuestion()"
                                                    class="inline-flex items-center gap-1.5 pl-2 pr-2.5 py-1 rounded-md border border-lime-600 bg-lime-50/80 hover:bg-lime-100 text-lime-800 text-xs font-semibold shadow-2xs transition cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-lime-700 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M5 12h14"/>
                                                    <path d="M12 5v14"/>
                                                </svg>
                                                <span>Tambah Soal</span>
                                            </button>

                                            <button type="button" @click="clearAllQuizzes()"
                                                    class="inline-flex items-center gap-1.5 pl-2 pr-2.5 py-1 rounded-md text-xs font-semibold text-slate-600 bg-white hover:text-rose-700 hover:bg-rose-50 border border-slate-300 hover:border-rose-300 shadow-2xs transition cursor-pointer"
                                                    title="Hapus seluruh pertanyaan kuis">
                                                <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 6h18"/>
                                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                                    <line x1="10" y1="11" x2="10" y2="17"/>
                                                    <line x1="14" y1="11" x2="14" y2="17"/>
                                                </svg>
                                                <span>Kosongkan Soal</span>
                                            </button>
                                        </div>
                                    </template>

                                </div>

                                <!-- Live Preview View -->
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
                                            Tampilan di bawah adalah simulasi bagaimana keluarga balita & kader akan melihat kuis ini di Tumbuh Academy.
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
                                                        <div class="flex items-center space-x-2 p-2 rounded-lg text-slate-700"
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
                                                class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold cursor-pointer">
                                            <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="m12 19-7-7 7-7"/>
                                                <path d="M19 12H5"/>
                                            </svg>
                                            <span>Kembali ke Editor</span>
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
                        <div class="flex items-center space-x-2 text-xs text-slate-500">
                            <span class="font-medium" x-text="formData.category"></span>
                            <span>•</span>
                            <span class="font-mono" x-text="quizzes.length + ' Soal Kuis'"></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button type="button" @click="closeFormModal()"
                                    class="px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 hover:border-slate-400 text-slate-700 text-xs font-semibold transition cursor-pointer shadow-2xs">
                                Batal
                            </button>
                            <button type="button" @click="submitForm()"
                                    class="inline-flex items-center space-x-1.5 px-5 py-2 rounded-xl bg-lime-600 hover:bg-lime-700 text-white text-xs font-semibold shadow-xs transition cursor-pointer">
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
                                    <path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"/>
                                    <path d="M7 3v4a1 1 0 0 0 1 1h7"/>
                                </svg>
                                <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan Modul'"></span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
    </template>

    <!-- Quick Quiz Detail Modal (Table Read-Only Preview) -->
    <template x-teleport="body">
        <div id="quickQuizPreviewModalBackdrop" x-show="showQuickQuizModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm transform-gpu flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="showQuickQuizModal = false"
             @click.self="showQuickQuizModal = false">

            <div class="relative bg-white rounded-xl border border-slate-200 shadow-2xl max-w-xl w-full p-6 space-y-4"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.stop>
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs">
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3M12 17h.01"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Daftar Pertanyaan Kuis</h3>
                                <p class="text-[11px] text-slate-500 truncate max-w-sm" x-text="quickQuizVideo ? quickQuizVideo.title : ''"></p>
                            </div>
                        </div>
                        <button type="button" @click="showQuickQuizModal = false" class="text-slate-400 hover:text-slate-700 cursor-pointer">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6 6 18M6 6l12 12"/>
                            </svg>
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
                        <button type="button" @click.stop="openEditFromQuickPreview()"
                                class="inline-flex items-center space-x-1.5 text-xs text-lime-700 hover:text-lime-800 font-semibold cursor-pointer">
                            <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 20h9"/>
                                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                            </svg>
                            <span>Edit Soal Kuis Ini</span>
                        </button>
                        <button type="button" @click="showQuickQuizModal = false"
                                class="px-3.5 py-1.5 rounded-md border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
    </template>

</div>

@push('scripts')
<script>
    function videoManager() {
        return {
            showFormModal: false,
            isEdit: false,
            formActionUrl: '{{ route("admin.videos.store") }}',
            activeModalTab: 'video',
            activeQuizView: 'editor',
            validationError: '',
            showQuickQuizModal: false,
            quickQuizVideo: null,

            getYoutubeVideoId(url) {
                if (!url) return null;
                const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
                return match ? match[1] : null;
            },

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
                this.activeModalTab = 'video';
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
                this.activeModalTab = 'video';
                this.formData = {
                    id: video.id,
                    title: video.title,
                    category: video.category,
                    youtube_url: video.youtube_url,
                    summary: video.summary
                };

                // Deep copy quiz data into visual builder state
                let quizData = video.quiz_data;
                if (typeof quizData === 'string') {
                    try {
                        quizData = JSON.parse(quizData);
                    } catch (e) {
                        quizData = [];
                    }
                }

                if (quizData && Array.isArray(quizData) && quizData.length > 0) {
                    this.quizzes = quizData.map(q => ({
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

            openEditFromQuickPreview() {
                if (!this.quickQuizVideo) return;
                const videoData = JSON.parse(JSON.stringify(this.quickQuizVideo));
                this.showQuickQuizModal = false;
                const quickBackdrop = document.getElementById('quickQuizPreviewModalBackdrop');
                if (quickBackdrop) {
                    quickBackdrop.style.display = 'none';
                }
                this.quickQuizVideo = null;
                this.openEditModal(videoData);
                this.activeModalTab = 'quiz';
            },

            closeFormModal() {
                this.showFormModal = false;
                this.showQuickQuizModal = false;
                this.quickQuizVideo = null;
                const quickBackdrop = document.getElementById('quickQuizPreviewModalBackdrop');
                if (quickBackdrop) {
                    quickBackdrop.style.display = 'none';
                }
            },

            openQuickQuizPreview(video) {
                this.quickQuizVideo = video;
                const quickBackdrop = document.getElementById('quickQuizPreviewModalBackdrop');
                if (quickBackdrop) {
                    quickBackdrop.style.display = '';
                }
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
                // If core video fields are missing, switch to video tab first
                if (!this.formData.title || !this.formData.title.trim() || !this.formData.youtube_url || !this.formData.summary) {
                    this.activeModalTab = 'video';
                    this.$nextTick(() => {
                        const form = document.getElementById('videoFormElement');
                        if (form) form.reportValidity();
                    });
                    return;
                }

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
                        this.activeModalTab = 'quiz';
                        this.activeQuizView = 'editor';
                        this.validationError = 'Soal #' + (i + 1) + ' belum memiliki teks pertanyaan.';
                        return;
                    }

                    if (filledOpts.length < 2) {
                        this.activeModalTab = 'quiz';
                        this.activeQuizView = 'editor';
                        this.validationError = 'Soal #' + (i + 1) + ' harus memiliki minimal 2 pilihan jawaban yang terisi.';
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
