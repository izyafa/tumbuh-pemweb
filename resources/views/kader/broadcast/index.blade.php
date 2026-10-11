@extends('layouts.app')

@section('title', 'Broadcast Pengingat Posyandu via WhatsApp')
@section('page_title', 'Siaran Pengingat Hari Posyandu')

@section('page_badge')
    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
        WhatsApp Broadcast Engine
    </span>
@endsection

@section('content')
<div class="space-y-6" x-data="broadcastManager()">

    <!-- Header Description & Quick Mass Actions -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                    <h2 class="text-base font-bold text-slate-900">Pengingat Jadwal Penimbangan & Imunisasi Posyandu</h2>
                </div>
                <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
                    Kirimkan pesan pengingat resmi jadwal penimbangan dan imunisasi ke seluruh orang tua balita secara personal maupun serentak untuk memaksimalkan kehadiran (D/S).
                </p>
            </div>

            <!-- Top Primary Mass Dispatch Buttons -->
            <div class="flex items-center flex-wrap gap-2 shrink-0">
                <!-- Button 1: Send to all at once (Mass Runner Modal) -->
                <button type="button" @click="openMassBroadcast('all')"
                        class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition cursor-pointer">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>Kirim ke Semua Orang (<span x-text="children.length"></span> Kontak)</span>
                </button>

                <!-- Button 2: Share to Multiple Contacts / Group via WhatsApp Native Picker -->
                <a :href="getGroupWaLink()" target="_blank"
                   class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-md border border-emerald-300 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-semibold transition cursor-pointer"
                   title="Buka pemilih kontak WhatsApp untuk mengirim ke grup atau banyak kontak sekaligus">
                    <i data-lucide="share-2" class="w-3.5 h-3.5"></i>
                    <span>Bagikan Pengumuman Grup</span>
                </a>

                <!-- Button 3: Copy All Phone Numbers -->
                <button type="button" @click="copyAllPhoneNumbers()"
                        class="inline-flex items-center space-x-1 px-3 py-2 rounded-md border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium transition cursor-pointer"
                        title="Salin semua nomor telepon untuk membuat Broadcast List di WhatsApp">
                    <i data-lucide="copy" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span x-text="copiedAllPhones ? 'Nomor Tersalin!' : 'Salin Nomor HP'"></span>
                </button>
            </div>
        </div>

        <!-- Metric Badges -->
        <div class="pt-3 border-t border-slate-100 flex items-center flex-wrap gap-3 text-xs">
            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded bg-slate-100 text-slate-700 font-medium">
                <i data-lucide="users" class="w-3.5 h-3.5 text-slate-500"></i>
                <span>Total: <strong class="text-slate-900" x-text="children.length"></strong> Balita</span>
            </span>
            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded bg-emerald-50 text-emerald-800 font-medium border border-emerald-200">
                <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>Sudah Terkirim: <strong x-text="totalSentCount"></strong></span>
            </span>
            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded bg-amber-50 text-amber-800 font-medium border border-amber-200">
                <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-600"></i>
                <span>Belum Terkirim: <strong x-text="totalUnsentCount"></strong></span>
            </span>
            <template x-if="totalUnsentCount > 0">
                <button type="button" @click="openMassBroadcast('unsent')"
                        class="text-xs text-emerald-700 font-semibold hover:underline cursor-pointer">
                    &rarr; Kirim ke <span x-text="totalUnsentCount"></span> yang Belum Terkirim
                </button>
            </template>
        </div>
    </div>

    <!-- 2 Column Configuration & Preview -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left: Configuration Form (5 cols) -->
        <div class="lg:col-span-5 bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 border-b border-slate-100 pb-2 flex items-center space-x-1.5">
                <i data-lucide="calendar" class="w-4 h-4 text-emerald-600"></i>
                <span>Detail Jadwal Posyandu Berikutnya</span>
            </h3>

            <div class="space-y-3.5 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Hari & Tanggal Pelaksanaan:</label>
                    <input type="text" x-model="posyanduDate"
                           class="w-full px-3 py-2 rounded-md border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600"
                           placeholder="Contoh: Kamis, 15 Oktober 2026">
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
                    <textarea rows="3" x-model="additionalNotes"
                              class="w-full px-3 py-2 rounded-md border border-slate-300 text-xs text-slate-800 focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 leading-relaxed"
                              placeholder="Contoh: Jadwal pemberian Vitamin A & vaksin campak rutin. Mohon bawa Buku KIA/KMS balita."></textarea>
                </div>
            </div>

            <!-- Copy Template Action -->
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[11px] text-slate-500">Format otomatis disinkronkan</span>
                <button type="button" @click="copySampleMessage()"
                        class="px-3 py-1.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold flex items-center space-x-1 transition cursor-pointer">
                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                    <span x-text="copied ? 'Tersalin!' : 'Salin Template'"></span>
                </button>
            </div>
        </div>

        <!-- Right: Live Message Preview Card (7 cols) -->
        <div class="lg:col-span-7 bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center space-x-1.5">
                    <i data-lucide="message-circle-more" class="w-4 h-4 text-emerald-600"></i>
                    <span>Pratinjau Format Pesan WhatsApp</span>
                </h3>
                <div class="flex items-center space-x-1 text-[11px]">
                    <button type="button" @click="previewMode = 'personal'"
                            :class="previewMode === 'personal' ? 'bg-emerald-100 text-emerald-800 font-bold' : 'text-slate-500 hover:text-slate-800'"
                            class="px-2 py-0.5 rounded cursor-pointer transition">
                        Pesan Personal
                    </button>
                    <span>•</span>
                    <button type="button" @click="previewMode = 'group'"
                            :class="previewMode === 'group' ? 'bg-emerald-100 text-emerald-800 font-bold' : 'text-slate-500 hover:text-slate-800'"
                            class="px-2 py-0.5 rounded cursor-pointer transition">
                        Pesan Siaran Grup
                    </button>
                </div>
            </div>

            <!-- WhatsApp Chat Bubble Mockup -->
            <div class="bg-emerald-50/60 p-4 rounded-lg border border-emerald-200/80 font-sans text-xs space-y-2 text-slate-800">
                <div class="p-3.5 bg-white rounded-lg shadow-2xs border border-emerald-100 space-y-2 leading-relaxed">
                    <template x-if="previewMode === 'personal'">
                        <div>
                            <p>Halo Bapak/Ibu <strong>[Nama Orang Tua]</strong>,</p>
                            <p class="mt-1">
                                Mengingatkan kembali jadwal kegiatan penimbangan rutin & pemantauan tumbuh kembang ananda <strong class="text-emerald-800">[Nama Balita]</strong> di Posyandu:
                            </p>
                        </div>
                    </template>
                    <template x-if="previewMode === 'group'">
                        <div>
                            <p class="font-bold text-slate-900 uppercase">📢 PENGUMUMAN JADWAL POSYANDU {{ strtoupper($posyanduName) }}</p>
                            <p class="mt-1">
                                Yth. Seluruh Bapak/Ibu Keluarga Balita, mengingatkan kembali agenda penimbangan & pemantauan gizi bulan ini:
                            </p>
                        </div>
                    </template>

                    <div class="bg-slate-50 p-2.5 rounded border border-slate-200 text-[11px] space-y-1 font-mono">
                        <div>📅 <strong>Hari/Tgl:</strong> <span x-text="posyanduDate"></span></div>
                        <div>⏰ <strong>Waktu:</strong> <span x-text="posyanduTime"></span></div>
                        <div>📍 <strong>Tempat:</strong> <span x-text="posyanduLocation"></span></div>
                        <div x-show="additionalNotes">📝 <strong>Agenda:</strong> <span x-text="additionalNotes"></span></div>
                    </div>

                    <p>
                        Pemantauan rutin setiap bulan sangat penting untuk mendeteksi *weight faltering* dan mencegah stunting sejak dini.
                    </p>
                    <p class="text-[11px] text-slate-500 italic">
                        Sampai jumpa di Posyandu! Salam sehat dari Kader Posyandu {{ $posyanduName }}.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Parent Broadcast Dispatch List with Selection & Batch Actions -->
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden space-y-0">
        
        <!-- Header Bar -->
        <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center space-x-2">
                <i data-lucide="send" class="w-4 h-4 text-emerald-600"></i>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">
                    Daftar Kirim Pesan ke Orang Tua Balita
                </h3>
            </div>
            <span class="text-[11px] text-slate-500 font-mono" x-text="children.length + ' Orang Tua Terdata'"></span>
        </div>

        <!-- Filter Tabs & Batch Action Bar -->
        <div class="p-4 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-3 bg-slate-50/70">
            
            <!-- Left: Filter Tabs -->
            <div class="flex items-center space-x-1 text-xs">
                <button type="button" @click="statusFilter = 'all'"
                        :class="statusFilter === 'all' ? 'bg-white text-slate-900 font-bold shadow-2xs border border-slate-200' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-1.5 rounded-md transition cursor-pointer">
                    Semua (<span x-text="children.length"></span>)
                </button>
                <button type="button" @click="statusFilter = 'unsent'"
                        :class="statusFilter === 'unsent' ? 'bg-amber-100 text-amber-900 font-bold border border-amber-200' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-1.5 rounded-md transition cursor-pointer">
                    Belum Terkirim (<span x-text="totalUnsentCount"></span>)
                </button>
                <button type="button" @click="statusFilter = 'sent'"
                        :class="statusFilter === 'sent' ? 'bg-emerald-100 text-emerald-900 font-bold border border-emerald-200' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-1.5 rounded-md transition cursor-pointer">
                    Sudah Terkirim (<span x-text="totalSentCount"></span>)
                </button>
            </div>

            <!-- Right: Search & Selection Actions -->
            <div class="flex items-center space-x-2">
                <div class="relative w-full sm:w-64">
                    <input type="text" x-model="searchQuery" placeholder="Cari nama balita / orang tua..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-md border border-slate-300 bg-white text-slate-800 focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                    <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5"></i>
                </div>
            </div>
        </div>

        <!-- Sticky Selection Toolbar (Appears when 1 or more items are selected) -->
        <div x-show="selectedIds.length > 0" x-cloak
             class="bg-emerald-700 text-white px-5 py-2.5 flex items-center justify-between text-xs transition">
            <div class="flex items-center space-x-2">
                <i data-lucide="check-square" class="w-4 h-4 text-emerald-200"></i>
                <span><strong x-text="selectedIds.length"></strong> orang tua balita dipilih</span>
            </div>
            <div class="flex items-center space-x-2">
                <button type="button" @click="openMassBroadcast('selected')"
                        class="px-3 py-1 rounded bg-white text-emerald-900 font-bold hover:bg-emerald-50 transition cursor-pointer shadow-xs">
                    🚀 Kirim ke <span x-text="selectedIds.length"></span> Kontak Ini
                </button>
                <button type="button" @click="markSelectedAsSent()"
                        class="px-2.5 py-1 rounded bg-emerald-800 text-white hover:bg-emerald-900 transition cursor-pointer">
                    Tandai Terkirim
                </button>
                <button type="button" @click="selectedIds = []"
                        class="px-2 py-1 text-emerald-200 hover:text-white cursor-pointer">
                    Batal
                </button>
            </div>
        </div>

        <!-- Table of Recipients -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <!-- Select All Checkbox -->
                        <th class="py-2.5 px-3 text-center w-10">
                            <input type="checkbox" :checked="isAllSelected" @change="toggleSelectAll()"
                                   title="Pilih Semua Orang Tua"
                                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        </th>
                        <th class="py-2.5 px-3 text-center w-24">Status</th>
                        <th class="py-2.5 px-4 min-w-[180px]">Nama Balita</th>
                        <th class="py-2.5 px-3">Usia Saat Ini</th>
                        <th class="py-2.5 px-4 min-w-[160px]">Nama Orang Tua</th>
                        <th class="py-2.5 px-3 font-mono">Nomor WhatsApp</th>
                        <th class="py-2.5 px-4 text-right">Aksi Personal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="ch in filteredChildren" :key="ch.id">
                        <tr class="hover:bg-slate-50/50 transition-colors"
                            :class="{
                                'bg-emerald-50/25': sentStatus[ch.id],
                                'bg-lime-50/40': selectedIds.includes(ch.id)
                            }">
                            <!-- Row Selection Checkbox -->
                            <td class="py-2.5 px-3 text-center">
                                <input type="checkbox" :value="ch.id" x-model="selectedIds"
                                       class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            </td>

                            <!-- Sent Status Badge & Toggle -->
                            <td class="py-2.5 px-3 text-center">
                                <button type="button" @click="toggleSent(ch.id)"
                                        class="inline-flex items-center space-x-1 px-2 py-0.5 rounded text-[10px] font-semibold transition cursor-pointer"
                                        :class="sentStatus[ch.id] ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'"
                                        title="Klik untuk mengubah status terkirim secara manual">
                                    <i data-lucide="check" class="w-3 h-3" x-show="sentStatus[ch.id]"></i>
                                    <span x-text="sentStatus[ch.id] ? 'Terkirim' : 'Belum'"></span>
                                </button>
                            </td>

                            <!-- Child Name -->
                            <td class="py-2.5 px-4 font-semibold text-slate-900">
                                <a :href="'/kader/children/' + ch.id" target="_blank" class="hover:text-emerald-700 transition" x-text="ch.full_name"></a>
                            </td>

                            <!-- Age -->
                            <td class="py-2.5 px-3 text-slate-600" x-text="ch.age_in_months + ' Bulan'"></td>

                            <!-- Parent Name -->
                            <td class="py-2.5 px-4 text-slate-700" x-text="ch.parent_name"></td>

                            <!-- Phone -->
                            <td class="py-2.5 px-3 font-mono text-slate-600" x-text="ch.parent_phone || '-'"></td>

                            <!-- Action Button -->
                            <td class="py-2.5 px-4 text-right">
                                <a :href="getWaLink(ch)" target="_blank" @click="markSent(ch.id)"
                                   class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-md text-xs font-semibold transition shadow-2xs cursor-pointer"
                                   :class="sentStatus[ch.id] ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-emerald-600 hover:bg-emerald-700 text-white'">
                                    <i data-lucide="message-circle-more" class="w-3.5 h-3.5"></i>
                                    <span x-text="sentStatus[ch.id] ? 'Kirim Ulang' : 'Kirim WhatsApp'"></span>
                                </a>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filteredChildren.length === 0">
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">
                                Tidak ada data orang tua balita yang sesuai dengan filter.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Table Footer Bulk Actions -->
        <div class="p-4 border-t border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <div class="flex items-center space-x-2">
                <button type="button" @click="selectAllFiltered()" class="text-slate-600 hover:text-slate-900 font-medium cursor-pointer">
                    Pilih Semua Hasil Filter
                </button>
                <span>•</span>
                <button type="button" @click="selectUnsentOnly()" class="text-emerald-700 hover:underline font-semibold cursor-pointer">
                    Pilih Yang Belum Terkirim Saja
                </button>
            </div>

            <div class="flex items-center space-x-2">
                <button type="button" @click="resetAllStatus()" class="text-slate-400 hover:text-rose-600 transition cursor-pointer">
                    Reset Status Terkirim
                </button>
            </div>
        </div>
    </div>

    <!-- MASS BROADCAST RUNNER MODAL (Kirim ke Semua Orang Sekaligus) -->
    <div x-show="showMassModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
         @keydown.escape.window="closeMassModal()">
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xl max-w-2xl w-full max-h-[92vh] flex flex-col overflow-hidden"
             @click.away="closeMassModal()">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-200 bg-emerald-700 text-white flex items-center justify-between shrink-0">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-800 text-emerald-100 flex items-center justify-center font-bold text-sm">
                        <i data-lucide="send" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Pengiriman Siaran WhatsApp Massal</h3>
                        <p class="text-[11px] text-emerald-200">
                            Antrean otomatis siaran pesan pengingat ke <span x-text="runnerQueue.length"></span> orang tua balita.
                        </p>
                    </div>
                </div>
                <button type="button" @click="closeMassModal()" class="text-emerald-200 hover:text-white p-1 rounded-md transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Content (Scrollable) -->
            <div class="p-6 overflow-y-auto space-y-5 flex-1 text-xs">

                <!-- Progress Header Bar -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-700">
                        <span>Kemajuan Pengiriman:</span>
                        <span class="font-mono text-emerald-800">
                            <span x-text="processedCount"></span> / <span x-text="runnerQueue.length"></span> Kontak (<span x-text="progressPercent + '%'"></span>)
                        </span>
                    </div>
                    <!-- Visual Progress Bar -->
                    <div class="w-full h-3 rounded-full bg-slate-100 border border-slate-200 overflow-hidden">
                        <div class="h-full bg-emerald-600 transition-all duration-300" :style="'width: ' + progressPercent + '%'"></div>
                    </div>
                </div>

                <!-- Active Target Spotlight Card (When in Progress) -->
                <template x-if="!isRunnerFinished && currentTarget">
                    <div class="p-4 rounded-xl border-2 border-emerald-400 bg-emerald-50/40 space-y-3">
                        <div class="flex items-center justify-between border-b border-emerald-200/60 pb-2">
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-600 text-white uppercase">Kontak Aktif</span>
                                <span class="text-xs font-bold text-slate-900" x-text="'Antrean #' + (runnerIndex + 1) + ' dari ' + runnerQueue.length"></span>
                            </div>
                            <span class="font-mono text-xs text-slate-600 font-semibold" x-text="currentTarget.parent_phone"></span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-slate-500 block text-[10px]">Nama Balita:</span>
                                <strong class="text-slate-900 text-sm" x-text="currentTarget.full_name"></strong>
                                <span class="text-[11px] text-slate-500 block" x-text="currentTarget.age_in_months + ' Bulan'"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[10px]">Nama Orang Tua / Wali:</span>
                                <strong class="text-slate-900 text-sm" x-text="currentTarget.parent_name"></strong>
                            </div>
                        </div>

                        <!-- Big Action Button to Trigger Current Contact & Step Forward -->
                        <div class="pt-2">
                            <button type="button" @click="sendCurrentAndNext()"
                                    class="w-full py-2.5 px-4 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center justify-center space-x-2 shadow-sm transition cursor-pointer">
                                <i data-lucide="message-circle-more" class="w-4 h-4"></i>
                                <span>Buka WhatsApp Kontak Ini & Lanjut ke Berikutnya &rarr;</span>
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Finished Celebration State -->
                <template x-if="isRunnerFinished">
                    <div class="p-6 rounded-xl border border-emerald-200 bg-emerald-50/70 text-center space-y-3">
                        <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 mx-auto flex items-center justify-center">
                            <i data-lucide="check-circle-2" class="w-7 h-7"></i>
                        </div>
                        <h4 class="text-sm font-bold text-slate-900">Seluruh Siaran WhatsApp Selesai Diproses!</h4>
                        <p class="text-xs text-slate-600 max-w-md mx-auto">
                            Sebanyak <strong x-text="processedCount"></strong> pesan pengingat jadwal Posyandu telah dibuka dan dikirimkan ke orang tua balita.
                        </p>
                        <div class="pt-2">
                            <button type="button" @click="closeMassModal()"
                                    class="px-5 py-2 rounded-md bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold cursor-pointer transition">
                                Selesai & Tutup Jendela
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Controls & Modes Section -->
                <template x-if="!isRunnerFinished">
                    <div class="space-y-3 pt-1">
                        <div class="flex items-center justify-between text-xs border-b border-slate-100 pb-2">
                            <span class="font-bold uppercase tracking-wider text-slate-700">Pilihan Cara Pengiriman:</span>
                            
                            <div class="flex items-center space-x-1">
                                <button type="button" @click="runnerMode = 'step'"
                                        :class="runnerMode === 'step' ? 'bg-slate-800 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                        class="px-2.5 py-1 rounded text-[11px] transition cursor-pointer">
                                    1-Klik Per Kontak (Aman)
                                </button>
                                <button type="button" @click="runnerMode = 'auto'"
                                        :class="runnerMode === 'auto' ? 'bg-slate-800 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                        class="px-2.5 py-1 rounded text-[11px] transition cursor-pointer">
                                    Otomatis Berurutan
                                </button>
                            </div>
                        </div>

                        <!-- Auto Runner Mode Controls -->
                        <template x-if="runnerMode === 'auto'">
                            <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="font-semibold text-slate-800">Runner Otomatis Antrean:</span>
                                        <p class="text-[11px] text-slate-500">Membuka tab WhatsApp tiap 2.5 detik secara otomatis.</p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button type="button" x-show="!isAutoRunning" @click="startAutoRunner()"
                                                class="px-3.5 py-1.5 rounded bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition cursor-pointer flex items-center space-x-1">
                                            <i data-lucide="play" class="w-3.5 h-3.5"></i>
                                            <span>Mulai Otomatis</span>
                                        </button>
                                        <button type="button" x-show="isAutoRunning" @click="stopAutoRunner()"
                                                class="px-3.5 py-1.5 rounded bg-amber-600 hover:bg-amber-700 text-white font-bold transition cursor-pointer flex items-center space-x-1">
                                            <i data-lucide="pause" class="w-3.5 h-3.5"></i>
                                            <span>Jeda (Pause)</span>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-[10px] text-amber-700 italic">
                                    Catatan: Jika tab baru tidak terbuka otomatis, mohon izinkan "Pop-ups and redirects" pada pengaturan peramban/browser Anda.
                                </p>
                            </div>
                        </template>

                        <!-- Step Mode Controls -->
                        <template x-if="runnerMode === 'step'">
                            <div class="flex items-center justify-between pt-1">
                                <button type="button" @click="skipCurrent()"
                                        class="text-xs text-slate-500 hover:text-slate-800 transition cursor-pointer">
                                    Lewati Kontak Ini &rarr;
                                </button>
                                <button type="button" @click="markCurrentWithoutOpen()"
                                        class="text-xs text-emerald-700 font-semibold hover:underline cursor-pointer">
                                    Tandai Terkirim Tanpa Membuka WhatsApp
                                </button>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Queue Overview List -->
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <span class="font-bold uppercase tracking-wider text-[11px] text-slate-600 block">Daftar Antrean Kontak:</span>
                    <div class="max-h-44 overflow-y-auto divide-y divide-slate-100 border border-slate-200 rounded-lg">
                        <template x-for="(ch, idx) in runnerQueue" :key="'q_' + ch.id">
                            <div class="px-3 py-2 flex items-center justify-between text-xs"
                                 :class="{
                                     'bg-emerald-50 font-bold': idx === runnerIndex && !isRunnerFinished,
                                     'bg-slate-50/60 text-slate-400': sentStatus[ch.id] && idx !== runnerIndex
                                 }">
                                <div class="flex items-center space-x-2">
                                    <span class="font-mono text-[10px] text-slate-400" x-text="(idx + 1) + '.'"></span>
                                    <span x-text="ch.full_name"></span>
                                    <span class="text-[10px] text-slate-400" x-text="'(' + ch.parent_name + ')'"></span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <template x-if="idx === runnerIndex && !isRunnerFinished">
                                        <span class="px-2 py-0.5 rounded text-[10px] bg-emerald-600 text-white font-bold">Sedang Diproses</span>
                                    </template>
                                    <template x-if="sentStatus[ch.id] && (idx !== runnerIndex || isRunnerFinished)">
                                        <span class="px-2 py-0.5 rounded text-[10px] bg-emerald-100 text-emerald-800 font-semibold">✓ Terkirim</span>
                                    </template>
                                    <template x-if="!sentStatus[ch.id] && idx > runnerIndex">
                                        <span class="text-[10px] text-slate-400">Menunggu</span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3.5 border-t border-slate-200 bg-slate-50 flex items-center justify-between shrink-0">
                <span class="text-[11px] text-slate-500 font-mono">
                    Mode Pengiriman Massal Aktif
                </span>
                <button type="button" @click="closeMassModal()"
                        class="px-4 py-2 rounded-md border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold cursor-pointer">
                    Tutup
                </button>
            </div>

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
        copied: false,
        copiedAllPhones: false,
        previewMode: 'personal',
        searchQuery: '',
        statusFilter: 'all', // 'all', 'unsent', 'sent'
        selectedIds: [],
        sentStatus: JSON.parse(localStorage.getItem('tumbuh_sent_wa') || '{}'),
        children: @json($children),

        // Mass Broadcast Runner State
        showMassModal: false,
        runnerQueue: [],
        runnerIndex: 0,
        runnerMode: 'step', // 'step' (1-click next) or 'auto' (timer runner)
        isAutoRunning: false,
        autoTimer: null,

        get totalSentCount() {
            return this.children.filter(ch => !!this.sentStatus[ch.id]).length;
        },

        get totalUnsentCount() {
            return this.children.filter(ch => !this.sentStatus[ch.id]).length;
        },

        get filteredChildren() {
            let list = this.children;

            // Apply Status Filter Tab
            if (this.statusFilter === 'unsent') {
                list = list.filter(ch => !this.sentStatus[ch.id]);
            } else if (this.statusFilter === 'sent') {
                list = list.filter(ch => !!this.sentStatus[ch.id]);
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

        get isAllSelected() {
            const current = this.filteredChildren;
            if (current.length === 0) return false;
            return current.every(ch => this.selectedIds.includes(ch.id));
        },

        get currentTarget() {
            if (this.runnerIndex >= 0 && this.runnerIndex < this.runnerQueue.length) {
                return this.runnerQueue[this.runnerIndex];
            }
            return null;
        },

        get isRunnerFinished() {
            return this.runnerQueue.length > 0 && this.runnerIndex >= this.runnerQueue.length;
        },

        get processedCount() {
            return Math.min(this.runnerIndex, this.runnerQueue.length);
        },

        get progressPercent() {
            if (this.runnerQueue.length === 0) return 0;
            return Math.round((this.processedCount / this.runnerQueue.length) * 100);
        },

        toggleSelectAll() {
            const current = this.filteredChildren;
            if (this.isAllSelected) {
                const currentIds = current.map(ch => ch.id);
                this.selectedIds = this.selectedIds.filter(id => !currentIds.includes(id));
            } else {
                const newIds = new Set([...this.selectedIds, ...current.map(ch => ch.id)]);
                this.selectedIds = Array.from(newIds);
            }
        },

        selectAllFiltered() {
            this.selectedIds = this.filteredChildren.map(ch => ch.id);
        },

        selectUnsentOnly() {
            this.selectedIds = this.children.filter(ch => !this.sentStatus[ch.id]).map(ch => ch.id);
        },

        markSelectedAsSent() {
            this.selectedIds.forEach(id => {
                this.sentStatus[id] = true;
            });
            localStorage.setItem('tumbuh_sent_wa', JSON.stringify(this.sentStatus));
            this.selectedIds = [];
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
        },

        markSent(childId) {
            this.sentStatus[childId] = true;
            localStorage.setItem('tumbuh_sent_wa', JSON.stringify(this.sentStatus));
        },

        toggleSent(childId) {
            this.sentStatus[childId] = !this.sentStatus[childId];
            localStorage.setItem('tumbuh_sent_wa', JSON.stringify(this.sentStatus));
        },

        resetAllStatus() {
            confirmDelete(null, {
                title: 'Reset Status Terkirim?',
                message: 'Apakah Anda yakin ingin mengatur ulang tanda terkirim untuk seluruh orang tua balita?',
                confirmText: 'Ya, Reset Status',
                onConfirm: () => {
                    this.sentStatus = {};
                    localStorage.removeItem('tumbuh_sent_wa');
                    this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                }
            });
        },

        // Copy All Clean Phone Numbers to Clipboard
        copyAllPhoneNumbers() {
            const phones = this.children
                .map(ch => ch.parent_phone)
                .filter(p => !!p && p.trim() !== '')
                .join(', ');

            navigator.clipboard.writeText(phones).then(() => {
                this.copiedAllPhones = true;
                setTimeout(() => this.copiedAllPhones = false, 2500);
            });
        },

        // Copy Sample Message Template
        copySampleMessage() {
            let msg = `*Jadwal Posyandu {{ $posyanduName }}*\n\n`;
            msg += `📅 Tanggal: ${this.posyanduDate}\n`;
            msg += `⏰ Waktu: ${this.posyanduTime}\n`;
            msg += `📍 Lokasi: ${this.posyanduLocation}\n`;
            if (this.additionalNotes) msg += `📝 Agenda: ${this.additionalNotes}\n`;
            msg += `\nMohon membawa Buku KIA/KMS balita.`;

            navigator.clipboard.writeText(msg).then(() => {
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            });
        },

        // Open Mass Broadcast Modal
        openMassBroadcast(mode = 'all') {
            this.stopAutoRunner();

            if (mode === 'selected' && this.selectedIds.length > 0) {
                this.runnerQueue = this.children.filter(ch => this.selectedIds.includes(ch.id));
            } else if (mode === 'unsent') {
                this.runnerQueue = this.children.filter(ch => !this.sentStatus[ch.id]);
            } else {
                this.runnerQueue = [...this.children];
            }

            if (this.runnerQueue.length === 0) {
                return;
            }

            this.runnerIndex = 0;
            this.runnerMode = 'step';
            this.showMassModal = true;
            this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
        },

        closeMassModal() {
            this.stopAutoRunner();
            this.showMassModal = false;
        },

        // Send Current and Step to Next
        sendCurrentAndNext() {
            const current = this.currentTarget;
            if (!current) return;

            // Open WhatsApp link in new tab
            window.open(this.getWaLink(current), '_blank');

            // Mark as sent
            this.markSent(current.id);

            // Step to next
            this.runnerIndex++;
            this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
        },

        skipCurrent() {
            this.runnerIndex++;
            this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
        },

        markCurrentWithoutOpen() {
            const current = this.currentTarget;
            if (!current) return;
            this.markSent(current.id);
            this.runnerIndex++;
            this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
        },

        // Automatic Queue Runner
        startAutoRunner() {
            if (this.isRunnerFinished) return;
            this.isAutoRunning = true;

            const runStep = () => {
                if (!this.isAutoRunning || this.isRunnerFinished) {
                    this.stopAutoRunner();
                    return;
                }

                this.sendCurrentAndNext();

                if (!this.isRunnerFinished) {
                    this.autoTimer = setTimeout(runStep, 2500);
                } else {
                    this.stopAutoRunner();
                }
            };

            runStep();
        },

        stopAutoRunner() {
            this.isAutoRunning = false;
            if (this.autoTimer) {
                clearTimeout(this.autoTimer);
                this.autoTimer = null;
            }
        }
    };
}
</script>
@endpush
@endsection
