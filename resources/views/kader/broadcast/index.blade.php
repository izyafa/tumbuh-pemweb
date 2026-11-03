@extends('layouts.app')

@section('title', 'Broadcast Pengingat Posyandu via WhatsApp')
@section('page_title', 'Siaran Pengingat Hari Posyandu')

@section('content')
<div class="space-y-6" x-data="broadcastManager()">

    <!-- Header Description & Quick Mass Actions -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Pengingat Jadwal Penimbangan & Imunisasi Posyandu</h2>
                <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
                    Kirimkan pesan pengingat resmi jadwal penimbangan dan imunisasi ke seluruh orang tua balita secara personal maupun serentak untuk memaksimalkan kehadiran (D/S).
                </p>
            </div>

            <!-- Top Primary Mass Action -->
            <div class="flex items-center shrink-0">
                <a :href="getGroupWaLink()" target="_blank"
                   class="inline-flex items-center space-x-2 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition cursor-pointer"
                   title="Buka WhatsApp untuk membagikan pengumuman ke grup Posyandu atau banyak kontak sekaligus">
                    <i data-lucide="share-2" class="w-4 h-4"></i>
                    <span>Bagikan Pengumuman Grup</span>
                </a>
            </div>
        </div>

        <!-- Metric Badges -->
        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
            <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded bg-slate-100 text-slate-700 font-medium">
                <i data-lucide="users" class="w-3.5 h-3.5 text-slate-500"></i>
                <span>Total Sasaran: <strong class="text-slate-900" x-text="children.length"></strong> Orang Tua Balita</span>
            </span>
        </div>
    </div>

    <!-- 2 Column Configuration & Preview -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left: Configuration Form (5 cols) -->
        <div class="lg:col-span-5 bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 border-b border-slate-100 pb-2 flex items-center space-x-1.5">
                <i data-lucide="calendar-days" class="w-4 h-4 text-emerald-600"></i>
                <span>Detail Jadwal Posyandu</span>
            </h3>

            <div class="space-y-3.5 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Hari &amp; Tanggal Pelaksanaan:</label>
                    <div class="relative flex items-center">
                        <input type="text" x-model="posyanduDate"
                               class="w-full pl-3 pr-9 py-2 rounded-md border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600"
                               placeholder="Contoh: Kamis, 15 Oktober 2026">
                        <div class="absolute right-2.5 pointer-events-none text-slate-400 flex items-center justify-center">
                            <i data-lucide="calendar-days" class="w-4 h-4"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Waktu / Jam Layanan:</label>
                    <input type="text" x-model="posyanduTime"
                           class="w-full px-3 py-2 rounded-md border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600"
                           placeholder="Contoh: 08.30 - 11.30 WIB">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Lokasi Posyandu:</label>
                    <input type="text" x-model="posyanduLocation"
                           class="w-full px-3 py-2 rounded-md border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600"
                           placeholder="Contoh: Balai Warga RW 03">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Agenda / Pesan Khusus:</label>
                    <textarea rows="6" x-model="additionalNotes"
                              class="w-full px-3 py-2 rounded-md border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 leading-relaxed resize-y"
                              placeholder="Contoh: Jadwal pemberian Vitamin A & vaksin campak rutin. Mohon bawa Buku KIA/KMS balita."></textarea>
                    
                    <!-- Quick Preset Agenda Chips (Always stay directly below textarea) -->
                    <div class="mt-2 space-y-1">
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Pilih Template Agenda:</span>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" @click="setPresetAgenda('penimbangan')"
                                    class="px-2 py-1 rounded text-[11px] font-medium bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 text-slate-700 transition cursor-pointer">
                                Penimbangan &amp; KMS
                            </button>
                            <button type="button" @click="setPresetAgenda('vitamin_a')"
                                    class="px-2 py-1 rounded text-[11px] font-medium bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 text-slate-700 transition cursor-pointer">
                                Vitamin A &amp; Cacing
                            </button>
                            <button type="button" @click="setPresetAgenda('imunisasi')"
                                    class="px-2 py-1 rounded text-[11px] font-medium bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 text-slate-700 transition cursor-pointer">
                                Imunisasi Lengkap
                            </button>
                            <button type="button" @click="setPresetAgenda('pmt')"
                                    class="px-2 py-1 rounded text-[11px] font-medium bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 text-slate-700 transition cursor-pointer">
                                PMT &amp; Edukasi Gizi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Live Message Preview Card (7 cols) -->
        <div class="lg:col-span-7 bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center space-x-1.5">
                    <i data-lucide="message-circle-more" class="w-4 h-4 text-emerald-600"></i>
                    <span>Pratinjau Format Pesan</span>
                </h3>
                <div class="flex items-center space-x-1 text-[11px]">
                    <button type="button" @click="previewMode = 'group'"
                            :class="previewMode === 'group' ? 'bg-emerald-100 text-emerald-800 font-bold' : 'text-slate-500 hover:text-slate-800'"
                            class="px-2 py-0.5 rounded cursor-pointer transition">
                        Siaran Grup
                    </button>
                    <span>•</span>
                    <button type="button" @click="previewMode = 'personal'"
                            :class="previewMode === 'personal' ? 'bg-emerald-100 text-emerald-800 font-bold' : 'text-slate-500 hover:text-slate-800'"
                            class="px-2 py-0.5 rounded cursor-pointer transition">
                        Chat Personal
                    </button>
                </div>
            </div>

            <!-- Helper Note Eliminating State Switcher Confusion -->
            <div class="p-2.5 rounded-md bg-emerald-50 border border-emerald-200/70 text-[11px] text-emerald-950 flex items-start space-x-2">
                <i data-lucide="info" class="w-3.5 h-3.5 text-emerald-600 mt-0.5 shrink-0"></i>
                <div class="space-y-0.5 leading-relaxed">
                    <p>
                        <strong>Sistem Otomatis:</strong> Tombol <em>"Bagikan Pengumuman Grup"</em> di atas selalu otomatis menggunakan format siaran umum grup, sedangkan setiap tombol <em>"Kirim WhatsApp"</em> di tabel selalu otomatis format <em>chat personal</em> (menyapa nama orang tua &amp; balita).
                    </p>
                </div>
            </div>

            <!-- WhatsApp Chat Bubble Mockup -->
            <div class="bg-emerald-50/60 p-4 rounded-lg border border-emerald-200/80 font-sans text-xs space-y-2 text-slate-800 overflow-hidden">
                <div class="p-3.5 bg-white rounded-lg shadow-2xs border border-emerald-100 space-y-2.5 leading-relaxed max-w-full break-words">
                    <template x-if="previewMode === 'personal'">
                        <div>
                            <p class="break-words">Halo Bapak/Ibu <strong>[Nama Orang Tua]</strong>,</p>
                            <p class="mt-1 break-words">
                                Mengingatkan kembali jadwal kegiatan penimbangan rutin & pemantauan tumbuh kembang ananda <strong class="text-emerald-800">[Nama Balita]</strong> di Posyandu:
                            </p>
                        </div>
                    </template>
                    <template x-if="previewMode === 'group'">
                        <div>
                            <p class="font-bold text-slate-900 uppercase break-words">📢 PENGUMUMAN JADWAL POSYANDU {{ strtoupper($posyanduName) }}</p>
                            <p class="mt-1 break-words">
                                Yth. Seluruh Bapak/Ibu Keluarga Balita, mengingatkan kembali agenda penimbangan & pemantauan gizi bulan ini:
                            </p>
                        </div>
                    </template>

                    <!-- Structured Schedule Details Box -->
                    <div class="bg-slate-50/90 p-3 rounded-lg border border-slate-200 text-[11px] space-y-2">
                        <div class="flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-2 pb-2 border-b border-slate-200/70">
                            <div class="flex items-center space-x-1 text-slate-500 font-semibold sm:w-20 shrink-0">
                                <span>📅</span>
                                <span>Hari/Tgl:</span>
                            </div>
                            <div class="flex-1 text-slate-800 font-medium break-words" x-text="posyanduDate || '-'"></div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-2 pb-2 border-b border-slate-200/70">
                            <div class="flex items-center space-x-1 text-slate-500 font-semibold sm:w-20 shrink-0">
                                <span>⏰</span>
                                <span>Waktu:</span>
                            </div>
                            <div class="flex-1 text-slate-800 font-medium break-words" x-text="posyanduTime || '-'"></div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-2 pb-2 border-b border-slate-200/70">
                            <div class="flex items-center space-x-1 text-slate-500 font-semibold sm:w-20 shrink-0">
                                <span>📍</span>
                                <span>Tempat:</span>
                            </div>
                            <div class="flex-1 text-slate-800 font-medium break-words" x-text="posyanduLocation || '-'"></div>
                        </div>

                        <div x-show="additionalNotes" class="flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-2 pt-0.5">
                            <div class="flex items-center space-x-1 text-slate-500 font-semibold sm:w-20 shrink-0">
                                <span>📝</span>
                                <span>Agenda:</span>
                            </div>
                            <div class="flex-1 text-slate-800 leading-relaxed break-words whitespace-pre-line" x-text="additionalNotes"></div>
                        </div>
                    </div>

                    <p class="break-words">
                        Pemantauan rutin setiap bulan sangat penting untuk mendeteksi dini risiko stunting dan menjaga tumbuh kembang buah hati.
                    </p>
                    <p class="text-[11px] text-slate-500 italic break-words">
                        Sampai jumpa di Posyandu! Salam sehat dari Kader Posyandu {{ $posyanduName }}.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Parent Personal Follow-up Table (Chat Personal) -->
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden space-y-0">
        
        <!-- Header Bar -->
        <div class="px-5 py-3.5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-slate-50/50">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-200 shadow-2xs">
                    <i data-lucide="message-circle-more" class="w-4.5 h-4.5"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">
                        Daftar Kirim Pesan ke Orang Tua Balita
                    </h3>
                    <p class="text-[11px] text-slate-500 font-normal mt-0.5">
                        Gunakan tombol aksi untuk mengingatkan orang tua balita secara langsung satu per satu.
                    </p>
                </div>
            </div>
            <span class="text-[11px] text-slate-500 font-mono shrink-0" x-text="children.length + ' Orang Tua Terdata'"></span>
        </div>

        <!-- Search & Quick Age Filter Bar -->
        <div class="p-3.5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/70">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 flex-1">
                <!-- Search Input -->
                <div class="relative w-full sm:w-64">
                    <input type="text" x-model="searchQuery" placeholder="Cari nama balita, orang tua, kontak..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                    <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5"></i>
                </div>

                <!-- Age Category Filter -->
                <div class="w-full sm:w-48">
                    <select x-model="ageFilter"
                            class="w-full px-2.5 py-1.5 text-xs rounded-md border border-slate-300 bg-white text-slate-700 focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 cursor-pointer">
                        <option value="all">Semua Kelompok Usia</option>
                        <option value="0-5">0 - 5 Bulan (ASI Eksklusif)</option>
                        <option value="6-23">6 - 23 Bulan (MPASI / Baduta)</option>
                        <option value="24-59">24 - 59 Bulan (Balita)</option>
                    </select>
                </div>
            </div>

            <div class="text-xs text-slate-500 font-medium shrink-0">
                Menampilkan <strong class="text-slate-800" x-text="filteredChildren.length"></strong> dari <strong class="text-slate-800" x-text="children.length"></strong> orang tua
            </div>
        </div>

        <!-- Table of Recipients -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-2.5 px-3 text-center w-12">No.</th>
                        <th class="py-2.5 px-4 min-w-[180px]">Nama Balita</th>
                        <th class="py-2.5 px-3">Usia Saat Ini</th>
                        <th class="py-2.5 px-4 min-w-[160px]">Nama Orang Tua</th>
                        <th class="py-2.5 px-3 font-mono">Nomor WhatsApp</th>
                        <th class="py-2.5 px-4 text-right">Aksi Personal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="(ch, idx) in filteredChildren" :key="ch.id">
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <!-- Row Number -->
                            <td class="py-2.5 px-3 text-center font-mono text-slate-400 text-[11px]" x-text="idx + 1"></td>

                            <!-- Child Name -->
                            <td class="py-2.5 px-4 font-semibold text-slate-900">
                                <a :href="'/kader/children/' + ch.id" target="_blank" class="hover:text-emerald-700 transition" x-text="ch.full_name"></a>
                            </td>

                            <!-- Age -->
                            <td class="py-2.5 px-3 text-slate-600 tabular-nums" x-text="ch.age_in_months !== undefined ? ch.age_in_months + ' Bulan' : '-'"></td>

                            <!-- Parent Name -->
                            <td class="py-2.5 px-4 text-slate-700" x-text="ch.parent_name"></td>

                            <!-- Phone -->
                            <td class="py-2.5 px-3 font-mono text-slate-600">
                                <template x-if="ch.parent_phone && ch.parent_phone.trim() !== '' && ch.parent_phone !== '-'">
                                    <span class="text-slate-800" x-text="ch.parent_phone"></span>
                                </template>
                                <template x-if="!ch.parent_phone || ch.parent_phone.trim() === '' || ch.parent_phone === '-'">
                                    <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded text-[10px] font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M10.68 13.31a16 16 0 0 0 3.41 2.6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1 1.72 2v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.42 19.42 0 0 1-3.33-2.67m-2.67-3.34a19.79 19.79 0 0 1-3.07-8.63A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91"/>
                                            <line x1="2" x2="22" y1="2" y2="22"/>
                                        </svg>
                                        <span>Belum Ada Kontak</span>
                                    </span>
                                </template>
                            </td>

                            <!-- Action Button -->
                            <td class="py-2.5 px-4 text-right">
                                <template x-if="ch.parent_phone && ch.parent_phone.trim() !== '' && ch.parent_phone !== '-'">
                                    <a :href="getWaLink(ch)" target="_blank"
                                       class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-md text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white transition shadow-2xs cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>
                                            <path d="M8 12h.01"/>
                                            <path d="M12 12h.01"/>
                                            <path d="M16 12h.01"/>
                                        </svg>
                                        <span>Kirim WhatsApp</span>
                                    </a>
                                </template>
                                <template x-if="!ch.parent_phone || ch.parent_phone.trim() === '' || ch.parent_phone === '-'">
                                    <span class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-md text-xs font-medium bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed"
                                          title="Nomor WhatsApp orang tua belum terdaftar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15V5a2 2 0 0 0-2-2H9"/>
                                            <path d="m2 2 20 20"/>
                                            <path d="M3.6 3.6c-.4.3-.6.8-.6 1.4v16l4-4h10"/>
                                        </svg>
                                        <span>Kontak Kosong</span>
                                    </span>
                                </template>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filteredChildren.length === 0">
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">
                                Tidak ada data orang tua balita yang sesuai dengan pencarian atau filter.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
function broadcastManager() {
    return {
        posyanduDate: '{{ $defaultDate }}',
        posyanduTime: '{{ $defaultTime }}',
        posyanduLocation: '{{ $defaultLocation }}',
        additionalNotes: 'Mohon membawa Buku KIA/KMS balita. Tersedia penimbangan rutin, pengukuran Z-Score, dan konsultasi gizi.',
        previewMode: 'group',
        searchQuery: '',
        ageFilter: 'all', // 'all', '0-5', '6-23', '24-59'
        children: @json($children),

        init() {
            this.$watch('searchQuery', () => this.$nextTick(() => window.lucide?.createIcons()));
            this.$watch('ageFilter', () => this.$nextTick(() => window.lucide?.createIcons()));
        },

        setPresetAgenda(type) {
            const presets = {
                penimbangan: 'Penimbangan rutin berat badan, pengukuran tinggi/panjang badan, lingkar kepala, dan pembaruan grafik KMS digital.',
                vitamin_a: 'Bulan Vitamin A (kapsul biru 6-11 bulan & kapsul merah 12-59 bulan) serta pembagian obat cacing pencegahan stunting.',
                imunisasi: 'Pemberian imunisasi dasar lengkap dan vaksinasi lanjutan balita (Polio, DPT-HB-Hib, PCV, Campak-Rubela). Mohon bawa Buku KIA/KMS.',
                pmt: 'Pemberian Makanan Tambahan (PMT) kaya protein hewani untuk balita dan konseling gizi pencegahan stunting.'
            };
            this.additionalNotes = presets[type] || '';
        },

        get filteredChildren() {
            let list = this.children;

            // Apply Age Filter
            if (this.ageFilter === '0-5') {
                list = list.filter(ch => (ch.age_in_months ?? 0) <= 5);
            } else if (this.ageFilter === '6-23') {
                list = list.filter(ch => (ch.age_in_months ?? 0) >= 6 && (ch.age_in_months ?? 0) <= 23);
            } else if (this.ageFilter === '24-59') {
                list = list.filter(ch => (ch.age_in_months ?? 0) >= 24);
            }

            // Apply Search Query
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                list = list.filter(ch => 
                    ch.full_name.toLowerCase().includes(q) || 
                    ch.parent_name.toLowerCase().includes(q) ||
                    (ch.parent_phone && ch.parent_phone.includes(q))
                );
            }

            return list;
        },

        // Personal WhatsApp Link Generator
        getWaLink(child) {
            let phone = (child.parent_phone || '').replace(/[^0-9]/g, '');
            if (phone.startsWith('0')) {
                phone = '62' + phone.substring(1);
            }

            let msg = `Halo Bapak/Ibu ${child.parent_name},\n\n`;
            msg += `Mengingatkan kembali jadwal kegiatan penimbangan rutin & pemantauan tumbuh kembang ananda *${child.full_name}* di Posyandu:\n\n`;
            msg += `📅 Tanggal: ${this.posyanduDate}\n`;
            msg += `⏰ Waktu: ${this.posyanduTime}\n`;
            msg += `📍 Lokasi: ${this.posyanduLocation}\n`;
            if (this.additionalNotes) {
                msg += `📝 Agenda: ${this.additionalNotes}\n`;
            }
            msg += `\nPemantauan rutin sangat penting untuk mendeteksi dini risiko stunting dan menjaga kesehatan buah hati.\n\n`;
            msg += `Sampai jumpa di Posyandu! Salam hangat dari Kader Posyandu {{ $posyanduName }}.`;

            return `https://api.whatsapp.com/send?phone=${phone}&text=${encodeURIComponent(msg)}`;
        },

        // Multi-Contact / Group WhatsApp Link (Native Contact Picker)
        getGroupWaLink() {
            let msg = `*PENGUMUMAN JADWAL KEGIATAN POSYANDU {{ strtoupper($posyanduName) }}*\n\n`;
            msg += `Kepada Yth. Seluruh Orang Tua & Balita,\n`;
            msg += `Mengingatkan kembali agenda penimbangan rutin, pengukuran Z-Score WHO, dan imunisasi bulan ini:\n\n`;
            msg += `📅 Tanggal: ${this.posyanduDate}\n`;
            msg += `⏰ Waktu: ${this.posyanduTime}\n`;
            msg += `📍 Lokasi: ${this.posyanduLocation}\n`;
            if (this.additionalNotes) {
                msg += `📝 Agenda: ${this.additionalNotes}\n`;
            }
            msg += `\nMohon membawa Buku KIA/KMS balita. Kehadiran Bapak/Ibu sangat berharga untuk mencegah stunting sejak dini.\n\n`;
            msg += `Salam sehat dari Kader Posyandu {{ $posyanduName }}.`;

            return `https://api.whatsapp.com/send?text=${encodeURIComponent(msg)}`;
        }
    };
}
</script>
@endpush
@endsection
