<?php

namespace App\Http\Controllers;

use App\Services\WhoGrowthService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GrowthCalculatorController extends Controller
{
    protected WhoGrowthService $whoService;

    public function __construct(WhoGrowthService $whoService)
    {
        $this->whoService = $whoService;
    }

    /**
     * Show public interactive anthropometry calculator
     */
    public function index(): View
    {
        return view('kalkulator.index');
    }

    /**
     * Rapid calculation endpoint for public and AJAX usage
     */
    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'gender' => ['required', 'in:male,female'],
            'age_in_months' => ['nullable', 'integer', 'min:0', 'max:60'],
            'dob' => ['nullable', 'date'],
            'height' => ['required', 'numeric', 'min:30', 'max:150'],
            'weight' => ['required', 'numeric', 'min:1', 'max:50'],
        ]);

        $age = 0;
        if (isset($validated['age_in_months'])) {
            $age = (int) $validated['age_in_months'];
        } elseif (!empty($validated['dob'])) {
            $dob = Carbon::parse($validated['dob']);
            $age = (int) $dob->diffInMonths(Carbon::now());
        }

        $age = max(0, min(60, $age));
        $gender = $validated['gender'];
        $height = (float) $validated['height'];
        $weight = (float) $validated['weight'];

        $tbResult = $this->whoService->calculateZScoreTbU($gender, $age, $height);
        $bbResult = $this->whoService->calculateZScoreBbU($gender, $age, $weight);
        $bbtbResult = $this->whoService->calculateZScoreBbTb($gender, $height, $weight);

        // Overall risk summary
        $isStunted = in_array($tbResult['status'], ['stunted', 'severely_stunted']);
        $isWasted = in_array($bbtbResult['status'], ['wasted', 'severely_wasted']);
        $isUnderweight = in_array($bbResult['status'], ['underweight', 'severely_underweight']);

        $alertLevel = 'normal';
        $summaryTitle = 'Pertumbuhan Baik & Sesuai Baku WHO';
        $recommendation = 'Lanjutkan pemberian gizi seimbang kaya protein hewani dan bawa anak rutin ke Posyandu setiap bulan.';

        if ($tbResult['status'] === 'severely_stunted' || $bbtbResult['status'] === 'severely_wasted') {
            $alertLevel = 'danger';
            $summaryTitle = 'Peringatan Medis: Indikasi Masalah Gizi Akut / Kronis Berat';
            $recommendation = 'Segera bawa balita ke Puskesmas terdekat atau Dokter Spesialis Anak untuk pemeriksaan penanganan gizi klinis intensif.';
        } elseif ($isStunted || $isWasted || $isUnderweight) {
            $alertLevel = 'warning';
            $summaryTitle = 'Perlu Perhatian Khusus: Terindikasi Defisit Pertumbuhan';
            $recommendation = 'Prioritaskan konsumsi protein hewani (telur, hati ayam, ikan kembung) pada setiap kali makan utama serta konsultasikan dengan kader Posyandu.';
        }

        return response()->json([
            'age_in_months' => $age,
            'tb_u' => $tbResult,
            'bb_u' => $bbResult,
            'bb_tb' => $bbtbResult,
            'alert_level' => $alertLevel,
            'summary_title' => $summaryTitle,
            'recommendation' => $recommendation,
        ]);
    }
}
