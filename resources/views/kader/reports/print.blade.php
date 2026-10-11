<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan SKDN {{ $posyanduName }} — {{ $monthCarbon->translatedFormat('F Y') }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body { font-size: 11px; background: white !important; color: black !important; }
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
            @page { margin: 1.5cm; size: A4 landscape; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-4 sm:p-8 font-sans">

    <!-- Action Bar (Hidden on Print) -->
    <div class="max-w-5xl mx-auto mb-6 p-4 bg-white border border-slate-200 rounded-lg shadow-sm flex items-center justify-between no-print">
        <div class="flex items-center space-x-2">
            <span class="w-3 h-3 rounded-full bg-lime-600"></span>
            <span class="font-bold text-sm text-slate-800">Pratinjau Cetak / PDF Laporan Bulanan</span>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-4 py-2 bg-lime-600 hover:bg-lime-700 text-white font-bold text-xs rounded-md shadow-sm transition flex items-center space-x-1.5 cursor-pointer">
                <span>🖨️ Cetak / Simpan PDF</span>
            </button>
            <button onclick="window.close()" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-md transition cursor-pointer">
                Tutup
            </button>
        </div>
    </div>

    <!-- Official Document Paper -->
    <div class="max-w-5xl mx-auto bg-white p-8 border border-slate-200 shadow-sm rounded-lg print:shadow-none print:border-none print:p-0 space-y-6">

        <!-- Kop Surat Dokumen Resmi -->
        <div class="border-b-2 border-slate-900 pb-4 text-center space-y-1">
            <h1 class="text-base font-bold uppercase tracking-wider text-slate-900">
                Pemerintah Daerah • Dinas Kesehatan • Puskesmas Wilayah Binaan
            </h1>
            <h2 class="text-xl font-extrabold uppercase tracking-tight text-slate-900">
                Laporan Pemantauan Pertumbuhan Balita & Surveilans Gizi (SKDN)
            </h2>
            <div class="flex items-center justify-center space-x-4 text-xs font-medium text-slate-600 pt-1">
                <span>Posyandu: <strong>{{ $posyanduName }}</strong></span>
                <span>•</span>
                <span>Bulan Pelaporan: <strong>{{ $monthCarbon->translatedFormat('F Y') }}</strong></span>
                <span>•</span>
                <span>Tanggal Cetak: <strong>{{ now()->translatedFormat('d F Y') }}</strong></span>
            </div>
        </div>

        <!-- SKDN Key Indicators Summary Table -->
        <div class="space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">I. Rekapitulasi Indikator SKDN & Status Gizi</h3>
            <table class="w-full text-left text-xs border border-slate-300">
                <thead class="bg-slate-100 border-b border-slate-300 text-slate-700">
                    <tr>
                        <th class="py-2 px-3 border-r border-slate-300 text-center">Sasaran Balita (S)</th>
                        <th class="py-2 px-3 border-r border-slate-300 text-center">Punya KMS (K)</th>
                        <th class="py-2 px-3 border-r border-slate-300 text-center">Ditimbang (D)</th>
                        <th class="py-2 px-3 border-r border-slate-300 text-center">Cakupan (D/S)</th>
                        <th class="py-2 px-3 border-r border-slate-300 text-center">Naik BB (N)</th>
                        <th class="py-2 px-3 border-r border-slate-300 text-center">Keberhasilan (N/D)</th>
                        <th class="py-2 px-3 border-r border-slate-300 text-center">Stunting (&lt; -2 SD)</th>
                        <th class="py-2 px-3 text-center">Prevalensi (%)</th>
                    </tr>
                </thead>
                <tbody class="font-mono text-center divide-y divide-slate-200">
                    <tr class="font-bold text-slate-900">
                        <td class="py-2 px-3 border-r border-slate-200">{{ $totalChildren }}</td>
                        <td class="py-2 px-3 border-r border-slate-200">{{ $totalWithKms }}</td>
                        <td class="py-2 px-3 border-r border-slate-200 text-lime-700">{{ $totalMeasured }}</td>
                        <td class="py-2 px-3 border-r border-slate-200">{{ $dsCoverage }}%</td>
                        <td class="py-2 px-3 border-r border-slate-200 text-emerald-700">{{ $totalWeightGain }}</td>
                        <td class="py-2 px-3 border-r border-slate-200">{{ $ndSuccess }}%</td>
                        <td class="py-2 px-3 border-r border-slate-200 text-rose-700">{{ $stuntedCount }}</td>
                        <td class="py-2 px-3 text-rose-700">{{ $stuntingRate }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Detailed Registry Table -->
        <div class="space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">II. Daftar Hasil Pengukuran Antropometri Balita</h3>
            <table class="w-full text-left text-[11px] border border-slate-300">
                <thead class="bg-slate-100 border-b border-slate-300 text-slate-700">
                    <tr>
                        <th class="py-1.5 px-2 text-center w-8 border-r border-slate-300">No</th>
                        <th class="py-1.5 px-3 border-r border-slate-300">Nama Balita & NIK</th>
                        <th class="py-1.5 px-2 text-center border-r border-slate-300">L/P</th>
                        <th class="py-1.5 px-2 text-center border-r border-slate-300">Usia</th>
                        <th class="py-1.5 px-2 text-right border-r border-slate-300">TB (cm)</th>
                        <th class="py-1.5 px-2 text-right border-r border-slate-300">BB (kg)</th>
                        <th class="py-1.5 px-2 text-center border-r border-slate-300">Z-Score TB/U</th>
                        <th class="py-1.5 px-3 border-r border-slate-300">Status Stunting</th>
                        <th class="py-1.5 px-2 text-center border-r border-slate-300">Tren BB</th>
                        <th class="py-1.5 px-3">Nama Orang Tua & Alamat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($records as $idx => $r)
                        <tr>
                            <td class="py-1.5 px-2 text-center font-mono border-r border-slate-200">{{ $idx + 1 }}</td>
                            <td class="py-1.5 px-3 font-medium border-r border-slate-200">
                                <strong>{{ $r->child->full_name }}</strong>
                                <div class="text-[9px] text-slate-500 font-mono">{{ $r->child->nik }}</div>
                            </td>
                            <td class="py-1.5 px-2 text-center border-r border-slate-200">{{ $r->child->gender === 'male' ? 'L' : 'P' }}</td>
                            <td class="py-1.5 px-2 text-center font-mono border-r border-slate-200">{{ $r->age_in_months }} bln</td>
                            <td class="py-1.5 px-2 text-right font-mono border-r border-slate-200">{{ number_format($r->height, 1) }}</td>
                            <td class="py-1.5 px-2 text-right font-mono border-r border-slate-200">{{ number_format($r->weight, 2) }}</td>
                            <td class="py-1.5 px-2 text-center font-mono border-r border-slate-200">
                                {{ $r->zscore_tb_u > 0 ? '+' : '' }}{{ number_format($r->zscore_tb_u, 2) }}
                            </td>
                            <td class="py-1.5 px-3 border-r border-slate-200">
                                {{ $r->stunting_status_label }}
                            </td>
                            <td class="py-1.5 px-2 text-center font-semibold border-r border-slate-200 {{ $r->is_weight_stagnant ? 'text-amber-700' : 'text-emerald-700' }}">
                                {{ $r->is_weight_stagnant ? 'T (2T)' : 'N (Naik)' }}
                            </td>
                            <td class="py-1.5 px-3 text-slate-600">
                                {{ $r->child->parent_name }}
                                <div class="text-[9px] text-slate-400 truncate max-w-xs">{{ $r->child->address }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-4 text-center text-slate-400">Tidak ada riwayat penimbangan pada bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Official Signatures -->
        <div class="pt-8 grid grid-cols-2 text-center text-xs text-slate-700 gap-8">
            <div class="space-y-16">
                <div>
                    <p>Mengetahui,</p>
                    <p class="font-bold text-slate-900">Bidan Desa / Tenaga Pelaksana Gizi</p>
                </div>
                <div>
                    <p class="font-bold underline text-slate-900">( .................................................... )</p>
                    <p class="text-[10px] text-slate-500">NIP. .................................................</p>
                </div>
            </div>

            <div class="space-y-16">
                <div>
                    <p>{{ $monthCarbon->translatedFormat('F Y') }},</p>
                    <p class="font-bold text-slate-900">Ketua Kader Posyandu {{ $posyanduName }}</p>
                </div>
                <div>
                    <p class="font-bold underline text-slate-900">( {{ Auth::user()->name ?? 'Kader Posyandu' }} )</p>
                    <p class="text-[10px] text-slate-500">Kader Penanggung Jawab SIP</p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
