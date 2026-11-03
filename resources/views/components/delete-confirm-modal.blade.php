<!-- Reusable Global Delete Confirmation Modal -->
<div x-data="globalDeleteModal()"
     x-show="isOpen"
     x-cloak
     @keydown.escape.window="close()"
     @open-delete-modal.window="openFromEvent($event)"
     class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 sm:p-6"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="relative bg-white rounded-2xl border border-slate-200/80 shadow-2xl max-w-lg w-full overflow-hidden transform transition-all"
         @click.away="close()"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-3"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-3">

        <!-- Top Right Close Button -->
        <button type="button" 
                @click="close()" 
                :disabled="isSubmitting"
                class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-1.5 rounded-lg transition cursor-pointer disabled:opacity-40"
                title="Tutup dialog">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <!-- Main Content Area -->
        <div class="p-6 sm:p-7 space-y-4">
            
            <!-- Header Section: Icon + Title + Message -->
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 shadow-2xs shrink-0">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18"></path>
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                        <line x1="10" y1="11" x2="10" y2="17"></line>
                        <line x1="14" y1="11" x2="14" y2="17"></line>
                    </svg>
                </div>
                <div class="min-w-0 flex-1 pr-6">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight leading-snug" x-text="title">
                        Konfirmasi Hapus Data
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed" x-text="message">
                        Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
            </div>

            <!-- Record Details Box (Item Detail Preview) -->
            <div x-show="itemDetail" class="rounded-xl bg-slate-50 border border-slate-200/80 p-3.5 space-y-1.5">
                <div class="flex items-center space-x-1.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <span x-text="itemTag || 'Rincian Data yang Akan Dihapus'">Rincian Data yang Akan Dihapus</span>
                </div>
                <div class="text-xs sm:text-sm font-medium text-slate-800 leading-relaxed break-words pl-5" x-text="itemDetail"></div>
            </div>

            <!-- Danger Warning Callout -->
            <div class="flex items-start space-x-2.5 px-3.5 py-2.5 rounded-xl bg-rose-50/70 border border-rose-100 text-xs text-rose-700 leading-relaxed">
                <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <span><strong>Perhatian:</strong> Tindakan ini permanen dan tidak dapat dibatalkan.</span>
            </div>

        </div>

        <!-- Footer Actions Toolbar -->
        <div class="bg-slate-50/80 px-6 py-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5">
            <button type="button" 
                    @click="close()"
                    :disabled="isSubmitting"
                    class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 active:bg-slate-200 text-slate-700 text-xs sm:text-sm font-semibold transition cursor-pointer shadow-2xs text-center disabled:opacity-50">
                Batal
            </button>
            <button type="button" 
                    @click="confirm()"
                    :disabled="isSubmitting"
                    class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white text-xs sm:text-sm font-semibold shadow-xs transition cursor-pointer disabled:opacity-75 disabled:cursor-not-allowed">
                <!-- Spinner during submit -->
                <template x-if="isSubmitting">
                    <svg class="animate-spin -ml-0.5 mr-1.5 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </template>
                <!-- Trash icon when idle -->
                <template x-if="!isSubmitting">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18"></path>
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                    </svg>
                </template>
                <span x-text="isSubmitting ? 'Menghapus...' : confirmText"></span>
            </button>
        </div>
    </div>
</div>
