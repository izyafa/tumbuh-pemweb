@extends(Auth::check() ? 'layouts.app' : 'layouts.guest')

@section('title', 'Tumbuh Academy — Modul Edukasi Gizi & Cegah Stunting')
@section('page_title', 'Tumbuh Academy')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto" x-data="academyApp()">

    <!-- Header Banner -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-lime-600"></span>
                    <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider">Tumbuh Academy • Pusat Literasi Gizi Posyandu</h2>
                </div>
                <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
                    Modul video edukasi berbasis bukti klinis Kemenkes RI untuk kader dan orang tua. Pelajari teknik MPASI kaya protein hewani, manajemen ASI, sanitasi lingkungan, dan ikuti kuis pemahaman mandiri.
                </p>
            </div>
            @if(Auth::check() && Auth::user()->isAdmin())
                <div class="shrink-0">
                    <a href="{{ route('admin.videos') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-md bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition shadow-sm">
                        <i data-lucide="settings" class="w-3.5 h-3.5"></i>
                        <span>Kelola Video & Kuis</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- Search Bar Section -->
        <div class="pt-4 border-t border-slate-100">
            <form action="{{ route('edukasi.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2.5">
                @if($selectedCategory && $selectedCategory !== 'Semua Kategori')
                    <input type="hidden" name="category" value="{{ $selectedCategory }}">
                @endif
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <input type="text"
                           name="search"
                           value="{{ $search ?? '' }}"
                           placeholder="Cari topik edukasi... (contoh: MPASI, protein hewani, telur, ASI, diare, sanitasi)"
                           class="w-full pl-10 pr-10 py-2.5 rounded-lg border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-lime-500 focus:border-lime-500 transition shadow-xs placeholder-slate-400 bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800">
                    @if(!empty($search))
                        <a href="{{ route('edukasi.index', $selectedCategory && $selectedCategory !== 'Semua Kategori' ? ['category' => $selectedCategory] : []) }}" 
                           class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition"
                           title="Hapus kata kunci pencarian">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-lg bg-lime-600 hover:bg-lime-700 text-white text-xs sm:text-sm font-semibold transition flex items-center justify-center space-x-1.5 shadow-xs cursor-pointer">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        <span>Cari</span>
                    </button>
                    @if(!empty($search) || ($selectedCategory && $selectedCategory !== 'Semua Kategori'))
                        <a href="{{ route('edukasi.index') }}"
                           class="px-3.5 py-2.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs sm:text-sm font-medium transition flex items-center space-x-1 shadow-xs"
                           title="Reset pencarian dan filter">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span class="hidden sm:inline">Reset</span>
                        </a>
                    @endif
                </div>
            </form>

            <!-- Quick Popular Search Chips for Parents -->
            <div class="flex flex-wrap items-center gap-1.5 mt-3 text-[11px] text-slate-500">
                <span class="font-medium text-slate-400 mr-0.5">Topik Populer:</span>
                @php
                    $popularTags = ['Protein Hewani', 'MPASI', 'Telur & Lele', 'ASI Eksklusif', 'Pelekatan', 'Sanitasi & PHBS', 'Diare'];
                @endphp
                @foreach($popularTags as $tag)
                    @php
                        $tagParams = ['search' => $tag];
                        if ($selectedCategory && $selectedCategory !== 'Semua Kategori') {
                            $tagParams['category'] = $selectedCategory;
                        }
                    @endphp
                    <a href="{{ route('edukasi.index', $tagParams) }}"
                       class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-lime-100 hover:text-lime-800 text-slate-600 transition border border-slate-200/60 font-medium">
                        {{ $tag }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Category Filters -->
        <div class="flex flex-wrap items-center gap-2 pt-3 mt-3 border-t border-slate-100">
            <span class="text-xs font-semibold text-slate-500 mr-2">Kategori:</span>
            @foreach($categories as $cat)
                @php
                    $catParams = ['category' => $cat];
                    if (!empty($search)) {
                        $catParams['search'] = $search;
                    }
                @endphp
                <a href="{{ route('edukasi.index', $catParams) }}" 
                   class="px-3 py-1 rounded text-xs font-medium transition {{ $selectedCategory === $cat ? 'bg-lime-600 text-white font-semibold shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Active Search Filter Feedback Bar -->
    @if(!empty($search))
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 p-3.5 rounded-lg bg-lime-50/70 border border-lime-200 text-xs text-lime-900 shadow-xs">
            <div class="flex items-center space-x-2">
                <i data-lucide="info" class="w-4 h-4 text-lime-700 shrink-0"></i>
                <span>
                    Menampilkan hasil pencarian untuk: <strong class="font-bold underline decoration-lime-500">"{{ $search }}"</strong>
                    @if($selectedCategory !== 'Semua Kategori')
                        pada kategori <strong class="font-bold">{{ $selectedCategory }}</strong>
                    @endif
                    — <span class="font-bold">{{ $videos->total() }} modul</span> ditemukan.
                </span>
            </div>
            <div class="flex items-center space-x-2 shrink-0">
                @if($selectedCategory !== 'Semua Kategori')
                    <a href="{{ route('edukasi.index', ['search' => $search]) }}" 
                       class="text-lime-700 hover:underline font-semibold text-[11px]">
                        Cari di Semua Kategori &rarr;
                    </a>
                @endif
                <a href="{{ route('edukasi.index', $selectedCategory !== 'Semua Kategori' ? ['category' => $selectedCategory] : []) }}" 
                   class="inline-flex items-center space-x-1 px-2.5 py-1 rounded bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-[11px] font-semibold transition">
                    <i data-lucide="x" class="w-3 h-3"></i>
                    <span>Hapus Kata Kunci</span>
                </a>
            </div>
        </div>
    @endif

    <!-- Video Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($videos as $vid)
            <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs hover:shadow-lg transition-all duration-300 flex flex-col overflow-hidden hover:-translate-y-1 group">
                <!-- Video Media Area: Live Inline Player OR High-Res Thumbnail Preview -->
                <div class="relative w-full aspect-video bg-black overflow-hidden">
                    <!-- Inline Live Player (Active when clicked) -->
                    <template x-if="inlinePlayingId === '{{ $vid->id }}'">
                        <div class="w-full h-full relative">
                            <iframe src="https://www.youtube-nocookie.com/embed/{{ $vid->youtube_id }}?autoplay=1&rel=0&enablejsapi=1" 
                                    title="{{ $vid->title }}" 
                                    class="w-full h-full border-0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                    allowfullscreen></iframe>
                        </div>
                    </template>

                    <!-- Thumbnail with Interactive Play Overlay -->
                    <div @click="openPlayer(@js($vid))"
                         class="relative w-full h-full cursor-pointer group/thumb"
                         title="Klik untuk menonton video">
                        <img src="{{ $vid->youtube_thumbnail_url }}" 
                             alt="{{ $vid->title }}" 
                             class="w-full h-full object-cover group-hover/thumb:scale-105 transition-transform duration-300"
                             loading="lazy">
                        
                        <!-- Dark Gradient Overlay & Big Red Play Button -->
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/25 to-transparent flex flex-col justify-between p-3.5 transition-opacity duration-200">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-black/60 text-white backdrop-blur-xs border border-white/10">
                                    <i data-lucide="video" class="w-3 h-3 text-lime-400"></i>
                                    <span>{{ $vid->category }}</span>
                                </span>
                                <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-lime-600/90 text-white shadow-xs">
                                    <i data-lucide="shield-check" class="w-3 h-3"></i>
                                    <span>Kemenkes RI</span>
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-10 h-10 rounded-full bg-rose-600 hover:bg-rose-500 text-white flex items-center justify-center shadow-md transform group-hover/thumb:scale-110 transition duration-200 ring-4 ring-rose-500/30">
                                        <i data-lucide="play" class="w-4 h-4 fill-white ml-0.5"></i>
                                    </div>
                                    <span class="text-xs font-semibold text-white drop-shadow-sm">
                                        Klik untuk menonton
                                    </span>
                                </div>
                                <span class="text-[10px] font-bold text-slate-200 bg-black/50 px-2 py-0.5 rounded border border-white/10">
                                    HD
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <!-- Category Badge with status dot -->
                            <span class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-lime-50 text-lime-800 border border-lime-200 uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-lime-600"></span>
                                <span>{{ $vid->category }}</span>
                            </span>
                        </div>
                        
                        <!-- Title (Clickable) -->
                        <a href="{{ route('edukasi.show', $vid->id) }}" class="block group/title">
                            <h3 class="text-sm font-bold text-slate-900 leading-snug group-hover/title:text-lime-700 transition line-clamp-2">
                                {{ $vid->title }}
                            </h3>
                        </a>
                    </div>

                    <!-- Action Buttons: Tonton Video di kiri & Detail Modul di kanan -->
                    <div class="pt-3 border-t border-slate-100">
                        <div class="grid grid-cols-2 gap-2.5">
                            <!-- Left: Tonton Video -->
                            <button type="button" @click="openPlayer(@js($vid))"
                                    class="w-full py-2.5 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 active:scale-[0.98] text-white text-xs font-bold transition flex items-center justify-center space-x-1.5 shadow-sm hover:shadow-md cursor-pointer group/btn">
                                <i data-lucide="play" class="w-3.5 h-3.5 text-lime-400 group-hover/btn:scale-110 transition-transform"></i>
                                <span>Tonton Video</span>
                            </button>

                            <!-- Right: Detail Modul (Vibrant Lime Green Primary) -->
                            <a href="{{ route('edukasi.show', $vid->id) }}"
                               class="w-full py-2.5 px-3 rounded-xl bg-lime-600 hover:bg-lime-700 active:scale-[0.98] text-white text-xs font-bold transition flex items-center justify-center space-x-1.5 shadow-sm hover:shadow-md cursor-pointer group/btn">
                                <span>Detail Modul</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-lime-200 group-hover/btn:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 px-6 text-center text-slate-400 bg-white rounded-lg border border-slate-200 shadow-sm space-y-3">
                <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 text-slate-400 flex items-center justify-center">
                    <i data-lucide="video-off" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-800">
                        @if(!empty($search))
                            Tidak ada modul video yang cocok dengan pencarian "{{ $search }}"
                        @else
                            Tidak ada modul video untuk kategori ini.
                        @endif
                    </p>
                    <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                        @if(!empty($search))
                            Coba gunakan kata kunci lain (misal: <em>MPASI, telur, ASI, sanitasi, diare</em>) atau cari di semua kategori.
                        @else
                            Belum ada modul yang tersedia di bawah kategori ini.
                        @endif
                    </p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('edukasi.index') }}" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-md bg-lime-600 hover:bg-lime-700 text-white text-xs font-semibold transition shadow-xs">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                        <span>Tampilkan Semua Modul Video</span>
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if($videos->hasPages())
        <div class="pt-8 flex justify-center">
            {{ $videos->links() }}
        </div>
    @endif

    <!-- Interactive Theater Player Modal (Video Only) -->
    <div x-show="showPlayerModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/85 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6"
         @keydown.escape.window="closePlayer()">
        <div class="bg-slate-900 rounded-2xl border border-slate-800 shadow-2xl max-w-3xl w-full overflow-hidden flex flex-col"
             @click.away="closePlayer()">
            
            <!-- Modal Header -->
            <div class="bg-slate-900 text-white px-4 py-2.5 flex items-center justify-between border-b border-slate-800 shrink-0">
                <div class="flex items-center space-x-2 min-w-0 pr-3">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-lime-500 text-slate-950 uppercase tracking-wide shrink-0"
                          x-text="activeVideo ? activeVideo.category : ''"></span>
                    <h3 class="text-xs sm:text-sm font-semibold text-white truncate" x-text="activeVideo ? activeVideo.title : ''"></h3>
                </div>
                <div class="flex items-center space-x-2 shrink-0">
                    <button type="button" @click="closePlayer()" 
                            class="p-1 rounded-md text-slate-400 hover:text-white hover:bg-slate-800 transition cursor-pointer"
                            title="Tutup Player (ESC)">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Video Player Screen -->
            <div class="bg-black relative w-full aspect-video shrink-0">
                <template x-if="activeVideo">
                    <iframe :src="getPlayerEmbedUrl(activeVideo)" 
                            :title="activeVideo.title" 
                            class="w-full h-full border-0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                            allowfullscreen></iframe>
                </template>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function academyApp() {
        return {
            showPlayerModal: false,
            activeVideo: null,
            inlinePlayingId: null,

            openPlayer(video) {
                this.activeVideo = video;
                this.showPlayerModal = true;
                this.$nextTick(() => {
                    if (window.refreshIcons) {
                        window.refreshIcons();
                    } else if (window.lucide && window.lucide.createIcons) {
                        window.lucide.createIcons();
                    }
                });
            },

            closePlayer() {
                this.showPlayerModal = false;
                this.activeVideo = null;
            },

            getPlayerEmbedUrl(video) {
                if (!video) return '';
                let id = video.youtube_id;
                if (!id && video.youtube_url) {
                    const match = video.youtube_url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
                    if (match && match[1]) id = match[1];
                }
                if (!id) return '';
                return 'https://www.youtube-nocookie.com/embed/' + id + '?autoplay=1&rel=0&enablejsapi=1';
            }
        }
    }
</script>
@endpush
@endsection
