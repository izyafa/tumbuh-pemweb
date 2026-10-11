@extends('layouts.app')

@section('title', 'Manajemen Pengguna & Kader')
@section('page_title', 'Pengguna & Kader Posyandu')

@section('page_badge')
    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-50 text-purple-800 border border-purple-200">
        Panel Administrator
    </span>
@endsection

@section('content')
<div class="space-y-6" x-data="userManager()">

    <!-- Header Actions -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Daftar Akun Petugas & Tenaga Medis</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola hak akses kader penimbang lapangan dan administrator sistem.</p>
        </div>
        <button type="button" @click="openCreateModal()"
                class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-md bg-lime-600 hover:bg-lime-700 text-white text-xs font-semibold shadow-sm transition">
            <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
            <span>Tambah Pengguna Baru</span>
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">Nama Lengkap</th>
                        <th class="py-3 px-3">Email</th>
                        <th class="py-3 px-3 text-center">Peran (Role)</th>
                        <th class="py-3 px-3">Posyandu Binaan</th>
                        <th class="py-3 px-3 font-mono">No. Telepon</th>
                        <th class="py-3 px-3">Terdaftar</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $user)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3 px-4 font-semibold text-slate-900">
                                {{ $user->name }}
                                @if(Auth::id() === $user->id)
                                    <span class="ml-1 text-[10px] text-lime-700 font-normal">(Anda)</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-slate-600 font-mono">{{ $user->email }}</td>
                            <td class="py-3 px-3 text-center">
                                @if($user->role === 'admin')
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 uppercase tracking-wider">
                                        Admin
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-lime-50 text-lime-800 border border-lime-200 uppercase tracking-wider">
                                        Kader
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-slate-600">{{ $user->posyandu_name ?? '-' }}</td>
                            <td class="py-3 px-3 text-slate-600 font-mono text-[11px]">{{ $user->phone ?? '-' }}</td>
                            <td class="py-3 px-3 text-slate-400 font-mono text-[11px]">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center space-x-2">
                                    <button type="button" @click="openEditModal(@js($user))" 
                                            class="p-1 text-slate-400 hover:text-slate-700" title="Edit">
                                        <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                    @if(Auth::id() !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                    onclick="confirmDelete(this, {
                                                        title: 'Hapus Akun Pengguna',
                                                        message: 'Apakah Anda yakin ingin menghapus pengguna ini? Akun tidak akan dapat login lagi ke dalam sistem.',
                                                        itemDetail: '{{ addslashes($user->name) }} ({{ $user->email }})',
                                                        confirmText: 'Ya, Hapus Pengguna'
                                                    })"
                                                    class="p-1 text-slate-400 hover:text-rose-600 transition cursor-pointer" title="Hapus">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-5 py-3 border-t border-slate-200 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Create User Modal -->
    <div x-show="showCreateModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg border border-slate-200 shadow-xl max-w-md w-full p-6 space-y-4" @click.away="showCreateModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">Tambah Akun Petugas Baru</h3>
                <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-700">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" required placeholder="Contoh: Siti Rahayu, A.Md.Keb" class="w-full px-3 py-1.5 rounded border border-slate-300 bg-white text-slate-800">
                </div>

                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">Email *</label>
                    <input type="email" name="email" required placeholder="Contoh: kader.melati@tumbuh.id" class="w-full px-3 py-1.5 rounded border border-slate-300 bg-white text-slate-800">
                </div>

                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">Kata Sandi (Min. 6 Karakter) *</label>
                    <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter sandi akun" class="w-full px-3 py-1.5 rounded border border-slate-300 bg-white text-slate-800">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">Peran (Role) *</label>
                        <select name="role" required class="w-full px-3 py-1.5 rounded border border-slate-300 bg-white text-slate-800">
                            <option value="kader">Kader Posyandu</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">No. WhatsApp</label>
                        <input type="text" name="phone" placeholder="Contoh: 081234567890" class="w-full px-3 py-1.5 rounded border border-slate-300 bg-white text-slate-800">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">Nama Posyandu Binaan</label>
                    <input type="text" name="posyandu_name" placeholder="Contoh: Posyandu Melati RW 03" class="w-full px-3 py-1.5 rounded border border-slate-300 bg-white text-slate-800">
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="showCreateModal = false" class="px-3 py-1.5 rounded text-slate-600 hover:text-slate-900">Batal</button>
                    <button type="submit" class="px-4 py-1.5 rounded bg-lime-600 hover:bg-lime-700 text-white font-semibold">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div x-show="showEditModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg border border-slate-200 shadow-xl max-w-md w-full p-6 space-y-4" @click.away="showEditModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">Perbarui Akun Petugas</h3>
                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-700">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form :action="'{{ url('admin/users') }}/' + (editingUser ? editingUser.id : '')" method="POST" class="space-y-3.5 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" x-model="editingUser.name" required placeholder="Contoh: Siti Rahayu, A.Md.Keb" class="w-full px-3 py-1.5 rounded border border-slate-300 bg-white text-slate-800">
                </div>

                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">Email *</label>
                    <input type="email" name="email" x-model="editingUser.email" required placeholder="Contoh: kader.melati@tumbuh.id" class="w-full px-3 py-1.5 rounded border border-slate-300 bg-white text-slate-800">
                </div>

                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">Ganti Kata Sandi (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" minlength="6" placeholder="Biarkan kosong untuk mempertahankan kata sandi lama" class="w-full px-3 py-1.5 rounded border border-slate-300 bg-white text-slate-800">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">Peran (Role) *</label>
                        <select name="role" x-model="editingUser.role" required class="w-full px-3 py-1.5 rounded border border-slate-300 bg-white text-slate-800">
                            <option value="kader">Kader Posyandu</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">No. WhatsApp</label>
                        <input type="text" name="phone" x-model="editingUser.phone" placeholder="Contoh: 081234567890" class="w-full px-3 py-1.5 rounded border border-slate-300 bg-white text-slate-800">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-700 mb-1">Nama Posyandu Binaan</label>
                    <input type="text" name="posyandu_name" x-model="editingUser.posyandu_name" placeholder="Contoh: Posyandu Melati RW 03" class="w-full px-3 py-1.5 rounded border border-slate-300 bg-white text-slate-800">
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="showEditModal = false" class="px-3 py-1.5 rounded text-slate-600 hover:text-slate-900">Batal</button>
                    <button type="submit" class="px-4 py-1.5 rounded bg-lime-600 hover:bg-lime-700 text-white font-semibold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function userManager() {
        return {
            showCreateModal: false,
            showEditModal: false,
            editingUser: {},

            openCreateModal() {
                this.showCreateModal = true;
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            },

            openEditModal(user) {
                this.editingUser = Object.assign({}, user);
                this.showEditModal = true;
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            }
        }
    }
</script>
@endpush
@endsection
