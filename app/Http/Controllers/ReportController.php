<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\MeasurementRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Display monthly Posyandu report overview & preview
     */
    public function index(Request $request): View
    {
        $selectedMonth = $request->input('month', Carbon::now()->format('Y-m'));
        $data = $this->getMonthlyReportData($selectedMonth);

        $startOfMonth = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        $endOfMonth = Carbon::createFromFormat('Y-m', $selectedMonth)->endOfMonth();

        $data['records'] = MeasurementRecord::with(['child', 'measuredBy'])
            ->whereBetween('measurement_date', [$startOfMonth, $endOfMonth])
            ->orderBy('measurement_date', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('kader.reports.index', $data);
    }

    /**
     * Printable official report view for browser Print to PDF
     */
    public function print(Request $request): View
    {
        $selectedMonth = $request->input('month', Carbon::now()->format('Y-m'));
        $data = $this->getMonthlyReportData($selectedMonth);

        return view('kader.reports.print', $data);
    }

    /**
     * Export monthly data as structured CSV for Excel
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $selectedMonth = $request->input('month', Carbon::now()->format('Y-m'));
        $data = $this->getMonthlyReportData($selectedMonth);

        $filename = "Laporan_Posyandu_TUMBUH_{$selectedMonth}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($data) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Title block
            fputcsv($handle, ['LAPORAN BULANAN PEMANTAUAN PERTUMBUHAN BALITA & GIZI (SKDN / SIP)']);
            fputcsv($handle, ['Posyandu:', $data['posyanduName']]);
            fputcsv($handle, ['Bulan Periode:', $data['monthCarbon']->translatedFormat('F Y')]);
            fputcsv($handle, ['Tanggal Unduh:', Carbon::now()->translatedFormat('d F Y H:i')]);
            fputcsv($handle, []);

            // Summary SKDN Metrics
            fputcsv($handle, ['--- INDIKATOR KINERJA SKDN & GIZI ---']);
            fputcsv($handle, ['Total Sasaran Balita (S)', $data['totalChildren']]);
            fputcsv($handle, ['Balita Memiliki KMS (K)', $data['totalWithKms']]);
            fputcsv($handle, ['Balita Ditimbang Bulan Ini (D)', $data['totalMeasured']]);
            fputcsv($handle, ['Balita Naik Berat Badan (N)', $data['totalWeightGain']]);
            fputcsv($handle, ['Balita BB Tidak Naik / 2T (T)', $data['totalStagnant']]);
            fputcsv($handle, ['Tingkat Kehadiran (D/S %)', $data['dsCoverage'] . '%']);
            fputcsv($handle, ['Tingkat Keberhasilan Pertumbuhan (N/D %)', $data['ndSuccess'] . '%']);
            fputcsv($handle, ['Balita Stunting (TB/U < -2 SD)', $data['stuntedCount']]);
            fputcsv($handle, ['Prevalensi Stunting (%)', $data['stuntingRate'] . '%']);
            fputcsv($handle, []);

            // Data Table Headers
            fputcsv($handle, [
                'No',
                'NIK Anak',
                'Nama Lengkap Balita',
                'Jenis Kelamin',
                'Tanggal Lahir',
                'Usia (Bulan)',
                'Nama Orang Tua',
                'No. HP Orang Tua',
                'Alamat',
                'Tanggal Penimbangan',
                'Berat Badan (kg)',
                'Tinggi Badan (cm)',
                'LiLA (cm)',
                'Lingkar Kepala (cm)',
                'Z-Score TB/U',
                'Status Stunting (TB/U)',
                'Z-Score BB/U',
                'Status BB (BB/U)',
                'Z-Score BB/TB',
                'Status Gizi (BB/TB)',
                'Kenaikan BB (2T)',
                'Petugas Penimbang',
                'Catatan Klinis',
            ]);

            $no = 1;
            foreach ($data['records'] as $rec) {
                fputcsv($handle, [
                    $no++,
                    "'" . $rec->child->nik, // leading tick prevents scientific notation in Excel
                    $rec->child->full_name,
                    $rec->child->gender === 'male' ? 'Laki-laki' : 'Perempuan',
                    $rec->child->date_of_birth->format('d/m/Y'),
                    $rec->age_in_months,
                    $rec->child->parent_name,
                    $rec->child->parent_phone,
                    $rec->child->address,
                    $rec->measurement_date->format('d/m/Y'),
                    number_format($rec->weight, 2),
                    number_format($rec->height, 1),
                    $rec->arm_circum ? number_format($rec->arm_circum, 1) : '-',
                    $rec->head_circum ? number_format($rec->head_circum, 1) : '-',
                    $rec->zscore_tb_u,
                    $rec->stunting_status_label,
                    $rec->zscore_bb_u,
                    $rec->bb_u_status_label ?? 'Normal',
                    $rec->zscore_bb_tb,
                    $rec->bb_tb_status_label ?? 'Normal',
                    $rec->is_weight_stagnant ? 'T (Tidak Naik / 2T)' : 'N (Naik)',
                    $rec->measuredBy->name ?? 'Kader Posyandu',
                    $rec->notes ?? '-',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Compute comprehensive metrics for monthly report
     */
    protected function getMonthlyReportData(string $selectedMonth): array
    {
        $startOfMonth = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        $endOfMonth = Carbon::createFromFormat('Y-m', $selectedMonth)->endOfMonth();

        $posyanduName = Auth::user()->posyandu_name ?? 'Posyandu Wilayah Binaan';

        $totalChildren = Child::count();
        $totalWithKms = $totalChildren; // All registered children have digital KMS

        // Records for this month
        $records = MeasurementRecord::with(['child', 'measuredBy'])
            ->whereBetween('measurement_date', [$startOfMonth, $endOfMonth])
            ->orderBy('measurement_date', 'asc')
            ->get();

        $totalMeasured = $records->unique('child_id')->count();
        $totalStagnant = $records->where('is_weight_stagnant', true)->unique('child_id')->count();
        $totalWeightGain = max(0, $totalMeasured - $totalStagnant);

        // Stunting indicators
        $stuntedRecords = $records->filter(function ($rec) {
            return in_array($rec->stunting_status, ['stunted', 'severely_stunted']);
        })->unique('child_id');
        $stuntedCount = $stuntedRecords->count();

        $severelyStuntedCount = $records->where('stunting_status', 'severely_stunted')->unique('child_id')->count();

        $stuntingRate = $totalMeasured > 0 ? round(($stuntedCount / $totalMeasured) * 100, 1) : 0;
        $dsCoverage = $totalChildren > 0 ? round(($totalMeasured / $totalChildren) * 100, 1) : 0;
        $ndSuccess = $totalMeasured > 0 ? round(($totalWeightGain / $totalMeasured) * 100, 1) : 0;

        return [
            'selectedMonth' => $selectedMonth,
            'monthCarbon' => $startOfMonth,
            'posyanduName' => $posyanduName,
            'records' => $records,
            'totalChildren' => $totalChildren,
            'totalWithKms' => $totalWithKms,
            'totalMeasured' => $totalMeasured,
            'totalWeightGain' => $totalWeightGain,
            'totalStagnant' => $totalStagnant,
            'stuntedCount' => $stuntedCount,
            'severelyStuntedCount' => $severelyStuntedCount,
            'stuntingRate' => $stuntingRate,
            'dsCoverage' => $dsCoverage,
            'ndSuccess' => $ndSuccess,
        ];
    }
}
