import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import { createIcons, icons } from 'lucide';

window.Alpine = Alpine;
window.Chart = Chart;
window.lucide = { createIcons, icons };

// Function to refresh Lucide icons anytime dynamic DOM updates happen
window.refreshIcons = () => {
    createIcons({ icons });
};

// Global Delete Confirmation Modal Handler
window.globalDeleteModal = function () {
    return {
        isOpen: false,
        title: 'Konfirmasi Hapus Data',
        message: 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
        itemDetail: '',
        confirmText: 'Ya, Hapus Data',
        targetForm: null,
        onConfirmCallback: null,

        openFromEvent(event) {
            const detail = event.detail || {};
            this.title = detail.title || 'Konfirmasi Hapus Data';
            this.message = detail.message || 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.';
            this.itemDetail = detail.itemDetail || '';
            this.confirmText = detail.confirmText || 'Ya, Hapus Data';
            this.targetForm = detail.form || null;
            this.onConfirmCallback = detail.onConfirm || null;
            this.isOpen = true;

            this.$nextTick(() => {
                if (window.lucide && window.lucide.createIcons) {
                    window.lucide.createIcons();
                }
            });
        },

        close() {
            this.isOpen = false;
            this.targetForm = null;
            this.onConfirmCallback = null;
        },

        confirm() {
            const form = this.targetForm;
            const callback = this.onConfirmCallback;
            this.isOpen = false;

            if (callback && typeof callback === 'function') {
                callback();
            } else if (form) {
                form.dataset.confirmed = 'true';
                form.submit();
            }
        }
    };
};

// Helper function to trigger delete modal from button/form
window.confirmDelete = function (target, options = {}) {
    let form = null;
    if (target instanceof HTMLFormElement) {
        form = target;
    } else if (target && target.target && target.target instanceof HTMLFormElement) {
        form = target.target;
    } else if (target instanceof HTMLElement) {
        form = target.closest('form');
    }

    const payload = {
        title: options.title || 'Konfirmasi Hapus Data',
        message: options.message || 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
        itemDetail: options.itemDetail || options.item || '',
        confirmText: options.confirmText || 'Ya, Hapus Data',
        form: form,
        onConfirm: typeof options === 'function' ? options : (options.onConfirm || null)
    };

    window.dispatchEvent(new CustomEvent('open-delete-modal', { detail: payload }));
    return false;
};

// Global interceptor for forms with data-confirm-delete attribute
document.addEventListener('submit', function (e) {
    const form = e.target;
    if (form && form.dataset && form.dataset.confirmDelete && !form.dataset.confirmed) {
        e.preventDefault();
        window.confirmDelete(form, {
            title: form.dataset.confirmTitle || 'Konfirmasi Hapus Data',
            message: form.dataset.confirmDelete,
            itemDetail: form.dataset.confirmDetail || '',
            confirmText: form.dataset.confirmButton || 'Ya, Hapus Data'
        });
    }
});

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});
