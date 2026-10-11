@extends('layouts.app')

@section('title', 'Input Cepat Penimbangan Posyandu')
@section('page_title', 'Pencatatan Penimbangan Massal (Batch Mode)')

@section('content')
<div x-data="batchEntry()" class="space-y-6">

    <!-- Information Header -->
    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Formulir Cepat Penimbangan Hari Posyandu</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Masukkan hasil penimbangan balita secara berurutan. Sistem secara otomatis menghitung Z-Score WHO & mendeteksi stunting atau risiko 2T secara real-time.
                </p>
            </div>
            <div class="flex items-center space-x-3 shrink-0">
                <label for="batch_date" class="text-xs font-semibold uppercase tracking-wider text-slate-600">Tanggal Pemeriksaan:</label>
                <input type="date" name="measurement_date" id="batch_date" x-model="measurementDate"
                       class="px-3 py-1.5 rounded-md border border-slate-300 text-xs text-slate-800 bg-white focus:outline-none focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
            </div>
        </div>
    </div>

    <!-- Main Table Form -->
    <form action="{{ route('kader.measurements.storeBatch') }}" method="POST">
        @csrf
        <input type="hidden" name="measurement_date" :value="measurementDate">

        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-600">
                            <th class="py-3 px-3 w-10 text-center">#</th>
                            <th class="py-3 px-4 min-w-[220px]">Pilih Balita</th>
                            <th class="py-3 px-3 w-28 text-right">BB (kg)</th>
                            <th class="py-3 px-3 w-28 text-right">TB / PB (cm)</th>
                            <th class="py-3 px-3 w-24 text-right">LK (cm)</th>
                            <th class="py-3 px-3 w-24 text-right">LiLA (cm)</th>
                            <th class="py-3 px-4 min-w-[200px] text-center">Preview Z-Score WHO</th>
                            <th class="py-3 px-3 min-w-[150px]">Catatan Kader</th>
                            <th class="py-3 px-3 w-12 text-center">Hapus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(row, index) in rows" :key="row.id">
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]" x-text="index + 1"></td>
                                
                                <!-- Child Selector -->
                                <td class="py-2.5 px-4">
                                    <select :name="'measurements[' + index + '][child_id]'" 
                                            x-model="row.child_id"
                                            @change="calculateRow(index)"
                                            required
                                            class="w-full py-1.5 px-2 text-xs rounded border border-slate-300 bg-white text-slate-800 focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                                        <option value="">-- Pilih Nama Balita --</option>
                                        @foreach($children as $ch)
                                            <option value="{{ $ch->id }}">
                                                {{ $ch->full_name }} ({{ $ch->gender === 'male' ? 'L' : 'P' }} • {{ $ch->date_of_birth->format('d/m/Y') }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <!-- Weight (BB) -->
                                <td class="py-2.5 px-3">
                                    <input type="number" step="0.01" min="1" max="50" placeholder="Contoh: 10.5"
                                           :name="'measurements[' + index + '][weight]'"
                                           x-model="row.weight"
                                           @input.debounce.400ms="calculateRow(index)"
                                           required
                                           class="w-full py-1.5 px-2 text-xs text-right font-mono font-medium rounded border border-slate-300 bg-white text-slate-800 focus:border-lime-600 focus:ring-1 focus:ring-lime-600 tabular-nums">
                                </td>

                                <!-- Height (TB) -->
                                <td class="py-2.5 px-3">
                                    <input type="number" step="0.1" min="30" max="150" placeholder="Contoh: 78.5"
                                           :name="'measurements[' + index + '][height]'"
                                           x-model="row.height"
                                           @input.debounce.400ms="calculateRow(index)"
                                           required
                                           class="w-full py-1.5 px-2 text-xs text-right font-mono font-medium rounded border border-slate-300 bg-white text-slate-800 focus:border-lime-600 focus:ring-1 focus:ring-lime-600 tabular-nums">
                                </td>

                                <!-- Head Circumference (LK) -->
                                <td class="py-2.5 px-3">
                                    <input type="number" step="0.1" min="20" max="70" placeholder="LK (Opsional)"
                                           :name="'measurements[' + index + '][head_circum]'"
                                           x-model="row.head_circum"
                                           class="w-full py-1.5 px-2 text-xs text-right font-mono rounded border border-slate-200 bg-white text-slate-600 focus:border-lime-600 focus:ring-1 focus:ring-lime-600 tabular-nums">
                                </td>

                                <!-- Arm Circumference (LiLA) -->
                                <td class="py-2.5 px-3">
                                    <input type="number" step="0.1" min="5" max="35" placeholder="LiLA (Opsional)"
                                           :name="'measurements[' + index + '][arm_circum]'"
                                           x-model="row.arm_circum"
                                           class="w-full py-1.5 px-2 text-xs text-right font-mono rounded border border-slate-200 bg-white text-slate-600 focus:border-lime-600 focus:ring-1 focus:ring-lime-600 tabular-nums">
                                </td>

                                <!-- Instant WHO Z-Score Preview Badge -->
                                <td class="py-2.5 px-4 text-center">
                                    <template x-if="row.loading">
                                        <span class="text-[11px] text-slate-400 italic">Menghitung...</span>
                                    </template>
                                    <template x-if="!row.loading && row.preview">
                                        <div class="flex flex-col items-center space-y-1">
                                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold border inline-block"
                                                  :class="{
                                                      'bg-emerald-50 text-emerald-700 border-emerald-200': row.preview.badge_type === 'green',
                                                      'bg-amber-50 text-amber-700 border-amber-200': row.preview.badge_type === 'yellow',
                                                      'bg-rose-50 text-rose-700 border-rose-200': row.preview.badge_type === 'red'
                                                  }">
                                                <span x-text="row.preview.tb_u.label"></span>
                                            </span>
                                            <div class="text-[10px] text-slate-500 font-mono flex items-center space-x-2">
                                                <span>TB/U: <strong x-text="(row.preview.tb_u.zScore > 0 ? '+' : '') + row.preview.tb_u.zScore"></strong> SD</span>
                                                <span x-show="row.preview.is_weight_stagnant" class="text-amber-600 font-semibold">⚠️ 2T Stagnan</span>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!row.loading && !row.preview">
                                        <span class="text-[11px] text-slate-300">Isi BB & TB untuk preview</span>
                                    </template>
                                </td>

                                <!-- Notes -->
                                <td class="py-2.5 px-3">
                                    <input type="text" placeholder="Contoh: Sehat, aktif, nafsu makan baik..."
                                           :name="'measurements[' + index + '][notes]'"
                                           x-model="row.notes"
                                           class="w-full py-1.5 px-2 text-xs rounded border border-slate-200 bg-white text-slate-700 focus:border-lime-600 focus:ring-1 focus:ring-lime-600">
                                </td>

                                <!-- Remove Button -->
                                <td class="py-2.5 px-3 text-center">
                                    <button type="button" @click="removeRow(index)" 
                                            class="p-1 rounded text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                            :disabled="rows.length === 1">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Table Action Buttons Footer -->
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                <button type="button" @click="addRow()"
                        class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-md border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium transition shadow-sm">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-lime-700"></i>
                    <span>Tambah Baris Balita (+1)</span>
                </button>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('kader.dashboard') }}" class="px-3.5 py-2 rounded-md text-xs font-medium text-slate-600 hover:text-slate-900 transition">
                        Batal
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center space-x-2 px-5 py-2 rounded-md bg-lime-600 hover:bg-lime-700 text-white text-xs font-semibold transition shadow-sm">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Simpan Seluruh Data Penimbangan</span>
                    </button>
                </div>
            </div>
        </div>
    </form>

</div>

@push('scripts')
<script>
    function batchEntry() {
        return {
            measurementDate: '{{ now()->toDateString() }}',
            rows: [
                { id: 1, child_id: '', weight: '', height: '', head_circum: '', arm_circum: '', notes: '', preview: null, loading: false },
                { id: 2, child_id: '', weight: '', height: '', head_circum: '', arm_circum: '', notes: '', preview: null, loading: false },
                { id: 3, child_id: '', weight: '', height: '', head_circum: '', arm_circum: '', notes: '', preview: null, loading: false }
            ],
            nextId: 4,

            addRow() {
                this.rows.push({
                    id: this.nextId++,
                    child_id: '',
                    weight: '',
                    height: '',
                    head_circum: '',
                    arm_circum: '',
                    notes: '',
                    preview: null,
                    loading: false
                });
                this.$nextTick(() => {
                    if (window.lucide) window.lucide.createIcons();
                });
            },

            removeRow(index) {
                if (this.rows.length > 1) {
                    this.rows.splice(index, 1);
                }
            },

            async calculateRow(index) {
                const row = this.rows[index];
                if (!row.child_id || !row.weight || !row.height || parseFloat(row.weight) <= 0 || parseFloat(row.height) <= 0) {
                    row.preview = null;
                    return;
                }

                row.loading = true;
                try {
                    const response = await fetch('{{ route("measurements.preview") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            child_id: row.child_id,
                            weight: row.weight,
                            height: row.height,
                            measurement_date: this.measurementDate
                        })
                    });

                    if (response.ok) {
                        const data = await response.json();
                        row.preview = data;
                    } else {
                        row.preview = null;
                    }
                } catch (e) {
                    console.error('Preview error:', e);
                } finally {
                    row.loading = false;
                }
            }
        }
    }
</script>
@endpush
@endsection
