<!-- Reusable Global Delete Confirmation Modal -->
<div x-data="globalDeleteModal()"
     x-show="isOpen"
     x-cloak
     @keydown.escape.window="close()"
     @open-delete-modal.window="openFromEvent($event)"
     class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="bg-white rounded-xl border border-slate-200 shadow-2xl max-w-md w-full p-6 space-y-4 transform"
         @click.away="close()"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2">

        <!-- Header with Icon & Title -->
        <div class="flex items-start space-x-3.5">
            <div class="w-10 h-10 rounded-full bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600 shrink-0">
                <i data-lucide="trash-2" class="w-5 h-5"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="text-sm font-bold text-slate-900 leading-snug" x-text="title">Konfirmasi Hapus Data</h3>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed" x-text="message">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>

                <!-- Optional Target Item Badge/Detail -->
                <template x-if="itemDetail">
                    <div class="mt-2.5 px-3 py-2 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-800 font-medium truncate flex items-center space-x-2">
                        <i data-lucide="file-text" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                        <span x-text="itemDetail" class="truncate font-mono text-[11px]"></span>
                    </div>
                </template>
            </div>
        </div>

        <!-- Warning Callout -->
        <div class="px-3 py-2 rounded-md bg-amber-50/70 border border-amber-200/80 text-[11px] text-amber-800 flex items-center space-x-2">
            <i data-lucide="alert-triangle" class="w-3.5 h-3.5 text-amber-600 shrink-0"></i>
            <span>Tindakan ini permanen dan tidak dapat dibatalkan.</span>
        </div>

        <!-- Modal Actions -->
        <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2.5">
            <button type="button" @click="close()"
                    class="px-4 py-2 rounded-md border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium transition cursor-pointer">
                Batal
            </button>
            <button type="button" @click="confirm()"
                    class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-md bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition cursor-pointer">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                <span x-text="confirmText"></span>
            </button>
        </div>
    </div>
</div>
