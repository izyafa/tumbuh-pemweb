<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Menuju Sehat (KMS) Digital — {{ $child->full_name }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body { font-size: 11px; background: white !important; color: black !important; }
            .no-print { display: none !important; }
            @page { margin: 1.2cm; size: A4 portrait; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-4 sm:p-8 font-sans">

    <!-- Action Bar (Hidden on Print) -->
    <div class="max-w-4xl mx-auto mb-6 p-4 bg-white border border-slate-200 rounded-lg shadow-sm flex items-center justify-between no-print">
        <div class="flex items-center space-x-2">
            <span class="w-3 h-3 rounded-full bg-lime-600"></span>
            <span class="font-bold text-sm text-slate-800">Pratinjau Cetak Kartu KMS Digital</span>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-4 py-2 bg-lime-600 hover:bg-lime-700 text-white font-bold text-xs rounded-md shadow-sm transition flex items-center space-x-1.5 cursor-pointer">
                <span>🖨️ Cetak / Simpan PDF</span>
            </button>
            <button onclick="window.history.back()" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition cursor-pointer">
                Kembali
            </button>
        </div>
    </div>

    <!-- Official KMS Sheet -->
    <div class="max-w-4xl mx-auto bg-white p-8 border border-slate-200 shadow-sm rounded-lg print:shadow-none print:border-none print:p-0 space-y-6">

        <!-- Kop Header -->
        <div class="border-b-2 border-slate-900 pb-3 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded bg-lime-600 flex items-center justify-center text-white font-black text-xl">
                    T
                </div>
                <div>
                    <h1 class="text-xs font-bold uppercase tracking-wider text-slate-600">Kementerian Kesehatan RI • Posyandu Terpadu</h1>
                    <h2 class="text-lg font-black uppercase tracking-tight text-slate-900">Kartu Menuju Sehat (KMS) Digital & Imunisasi</h2>
                </div>
            </div>
            <div class="text-right text-[11px] text-slate-500 font-mono">
                <div>Dokumen Resmi</div>
                <div class="font-bold text-slate-800">Standar WHO Anthro 2006</div>
            </div>
        </div>

        <!-- Identitas Balita -->
        <div class="bg-slate-50 border border-slate-200 rounded-md p-4 text-xs space-y-2">
            <div class="font-bold text-slate-900 uppercase tracking-wider text-[11px] border-b border-slate-200 pb-1">
                Biodata Anak & Orang Tua
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-slate-700">
                <div>
                    <span class="text-slate-400 block text-[10px]">Nama Lengkap:</span>
                    <strong class="text-slate-900 text-sm">{{ $child->full_name }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px]">NIK:</span>
                    <span class="font-mono font-bold text-slate-900">{{ $child->nik }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px]">Jenis Kelamin:</span>
                    <strong>{{ $child->gender_label }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px]">Tanggal Lahir:</span>
                    <strong>{{ $child->date_of_birth->translatedFormat('d F Y') }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px]">Berat / Panjang Lahir:</span>
                    <strong>{{ $child->birth_weight }} kg / {{ $child->birth_height }} cm</strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px]">Usia Saat Ini:</span>
                    <strong>{{ $child->age_in_months }} Bulan</strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px]">Nama Orang Tua:</span>
                    <strong>{{ $child->parent_name }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px]">Kontak / HP:</span>
                    <span class="font-mono">{{ $child->parent_phone }}</span>
                </div>
                <div class="col-span-2 sm:col-span-4">
                    <span class="text-slate-400 block text-[10px]">Alamat Domisili:</span>
                    <span>{{ $child->address }}</span>
                </div>
            </div>
        </div>

        <!-- Ringkasan Status Klinis Terakhir -->
        @if($latestMeasurement)
        <div class="p-3.5 rounded-md border text-xs flex items-center justify-between {{ $latestMeasurement->badge_class }}">
            <div>
                <span class="text-[10px] uppercase font-bold tracking-wider opacity-80">Status Pertumbuhan Terakhir:</span>
                <div class="font-bold text-sm">{{ $latestMeasurement->stunting_status_label }}</div>
                <div class="text-[11px] font-mono mt-0.5">
                    TB: {{ $latestMeasurement->height }} cm (Z: {{ $latestMeasurement->zscore_tb_u }} SD) • BB: {{ $latestMeasurement->weight }} kg (Z: {{ $latestMeasurement->zscore_bb_u }} SD)
                </div>
            </div>
            <div class="text-right font-medium">
                <div>Pemeriksaan: {{ $latestMeasurement->measurement_date->translatedFormat('d F Y') }}</div>
                <div>Kenaikan BB: {{ $latestMeasurement->is_weight_stagnant ? '⚠️ 2T (Stagnan)' : '✅ Normal' }}</div>
            </div>
        </div>
        @endif

        <!-- Tabel Riwayat Penimbangan -->
        <div class="space-y-1.5">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">I. Riwayat Penimbangan & Pengukuran Berkala</h3>
            <table class="w-full text-left text-[11px] border border-slate-300">
                <thead class="bg-slate-100 border-b border-slate-300 text-slate-700">
                    <tr>
                        <th class="py-1.5 px-2 text-center border-r border-slate-300">No</th>
                        <th class="py-1.5 px-3 border-r border-slate-300">Tgl Timbang</th>
                        <th class="py-1.5 px-2 text-center border-r border-slate-300">Usia</th>
                        <th class="py-1.5 px-2 text-right border-r border-slate-300">TB (cm)</th>
                        <th class="py-1.5 px-2 text-right border-r border-slate-300">BB (kg)</th>
                        <th class="py-1.5 px-2 text-center border-r border-slate-300">Z TB/U</th>
                        <th class="py-1.5 px-3 border-r border-slate-300">Status Stunting</th>
                        <th class="py-1.5 px-2 text-center border-r border-slate-300">2T</th>
                        <th class="py-1.5 px-3">Catatan / Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($child->measurementRecords as $idx => $m)
                        <tr>
                            <td class="py-1 px-2 text-center font-mono border-r border-slate-200">{{ $idx + 1 }}</td>
                            <td class="py-1 px-3 font-mono border-r border-slate-200">{{ $m->measurement_date->format('d/m/Y') }}</td>
                            <td class="py-1 px-2 text-center font-mono border-r border-slate-200">{{ $m->age_in_months }} bln</td>
                            <td class="py-1 px-2 text-right font-mono font-medium border-r border-slate-200">{{ number_format($m->height, 1) }}</td>
                            <td class="py-1 px-2 text-right font-mono font-medium border-r border-slate-200">{{ number_format($m->weight, 2) }}</td>
                            <td class="py-1 px-2 text-center font-mono border-r border-slate-200">{{ $m->zscore_tb_u > 0 ? '+' : '' }}{{ number_format($m->zscore_tb_u, 2) }}</td>
                            <td class="py-1 px-3 border-r border-slate-200">{{ $m->stunting_status_label }}</td>
                            <td class="py-1 px-2 text-center font-bold border-r border-slate-200 {{ $m->is_weight_stagnant ? 'text-amber-700' : 'text-emerald-700' }}">
                                {{ $m->is_weight_stagnant ? 'T' : 'N' }}
                            </td>
                            <td class="py-1 px-3 text-slate-500 text-[10px] truncate max-w-xs">{{ $m->notes ?? ($m->measuredBy->name ?? 'Kader') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-3 text-center text-slate-400">Belum ada riwayat penimbangan tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Tabel Riwayat Imunisasi & Vitamin A -->
        <div class="space-y-1.5">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">II. Riwayat Imunisasi Dasar & Suplementasi Vitamin A</h3>
            <table class="w-full text-left text-[11px] border border-slate-300">
                <thead class="bg-slate-100 border-b border-slate-300 text-slate-700">
                    <tr>
                        <th class="py-1.5 px-2 text-center border-r border-slate-300">No</th>
                        <th class="py-1.5 px-3 border-r border-slate-300">Jenis Vaksin / Suplementasi</th>
                        <th class="py-1.5 px-3 border-r border-slate-300">Kategori</th>
                        <th class="py-1.5 px-3 border-r border-slate-300">Tanggal Diberikan</th>
                        <th class="py-1.5 px-3 border-r border-slate-300">No. Batch</th>
                        <th class="py-1.5 px-3">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($child->immunizationRecords as $idx => $im)
                        <tr>
                            <td class="py-1 px-2 text-center font-mono border-r border-slate-200">{{ $idx + 1 }}</td>
                            <td class="py-1 px-3 font-bold border-r border-slate-200">{{ $im->vaccine_name }}</td>
                            <td class="py-1 px-3 text-slate-600 border-r border-slate-200">{{ $im->category_label }}</td>
                            <td class="py-1 px-3 font-mono border-r border-slate-200">{{ $im->administered_date->translatedFormat('d F Y') }}</td>
                            <td class="py-1 px-3 font-mono border-r border-slate-200">{{ $im->batch_number ?? '-' }}</td>
                            <td class="py-1 px-3 text-slate-500 text-[10px]">{{ $im->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-3 text-center text-slate-400">Belum ada riwayat imunisasi yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Signatures & Verification -->
        <div class="pt-6 grid grid-cols-2 text-center text-xs text-slate-700 gap-8">
            <div class="space-y-14">
                <div>
                    <p>Orang Tua / Wali Balita,</p>
                </div>
                <div>
                    <p class="font-bold underline text-slate-900">( {{ $child->parent_name }} )</p>
                </div>
            </div>

            <div class="space-y-14">
                <div>
                    <p>Posyandu Terpadu,</p>
                    <p class="font-bold text-slate-900">Kader Penanggung Jawab KMS</p>
                </div>
                <div>
                    <p class="font-bold underline text-slate-900">( .................................................... )</p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
