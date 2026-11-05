<?php

namespace App\Services;

use App\Models\MeasurementRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class WhoGrowthService
{
    protected ?array $standards = null;

    public function __construct()
    {
        $this->loadStandards();
    }

    protected function loadStandards(): void
    {
        $candidates = [
            function_exists('storage_path') ? storage_path('app/who_standards/growth_standards.json') : null,
            function_exists('resource_path') ? resource_path('data/who_standards/growth_standards.json') : null,
            dirname(__DIR__, 2) . '/storage/app/who_standards/growth_standards.json',
            dirname(__DIR__, 2) . '/resources/data/who_standards/growth_standards.json',
        ];

        foreach (array_filter($candidates) as $path) {
            if (File::exists($path)) {
                $this->standards = json_decode(File::get($path), true);
                return;
            }
        }
    }

    /**
     * Calculate age in completed months between DOB and measurement date
     */
    public function calculateAgeInMonths(Carbon $dob, Carbon $measurementDate): int
    {
        if ($measurementDate->lessThan($dob)) {
            return 0;
        }

        return (int) $dob->diffInMonths($measurementDate);
    }

    /**
     * Calculate Height-for-Age (TB/U) Z-Score & Status
     */
    public function calculateZScoreTbU(string $gender, int $ageInMonths, float $height): array
    {
        $genderKey = strtolower($gender) === 'female' ? 'female' : 'male';
        $ageClamped = max(0, min(60, $ageInMonths));

        $ref = $this->standards['tb_u'][$genderKey][(string) $ageClamped] ?? null;

        if (!$ref) {
            return [
                'zScore' => 0.0,
                'status' => 'normal',
                'label' => 'Normal',
            ];
        }

        $median = (float) $ref['median'];
        $sdMinus2 = (float) $ref['sd_minus_2'];
        $sdPlus2 = (float) $ref['sd_plus_2'];

        // Calculate standard deviation unit
        if ($height >= $median) {
            $sd = ($sdPlus2 - $median) / 2.0;
        } else {
            $sd = ($median - $sdMinus2) / 2.0;
        }

        $sd = $sd > 0 ? $sd : 1.0;
        $zScore = round(($height - $median) / $sd, 2);
        $zScore = max(-99.99, min(99.99, $zScore));

        $status = 'normal';
        $label = 'Normal';

        if ($zScore < -3.0) {
            $status = 'severely_stunted';
            $label = 'Sangat Pendek (Severely Stunted)';
        } elseif ($zScore < -2.0) {
            $status = 'stunted';
            $label = 'Pendek (Stunted)';
        } elseif ($zScore <= 2.0) {
            $status = 'normal';
            $label = 'Normal';
        } else {
            $status = 'tall';
            $label = 'Tinggi';
        }

        return [
            'zScore' => $zScore,
            'status' => $status,
            'label' => $label,
        ];
    }

    /**
     * Calculate Weight-for-Age (BB/U) Z-Score & Status
     */
    public function calculateZScoreBbU(string $gender, int $ageInMonths, float $weight): array
    {
        $genderKey = strtolower($gender) === 'female' ? 'female' : 'male';
        $ageClamped = max(0, min(60, $ageInMonths));

        $ref = $this->standards['bb_u'][$genderKey][(string) $ageClamped] ?? null;

        if (!$ref) {
            return [
                'zScore' => 0.0,
                'status' => 'normal',
                'label' => 'Normal',
            ];
        }

        $median = (float) $ref['median'];
        $sdMinus2 = (float) $ref['sd_minus_2'];
        $sdPlus2 = (float) $ref['sd_plus_2'];

        if ($weight >= $median) {
            $sd = ($sdPlus2 - $median) / 2.0;
        } else {
            $sd = ($median - $sdMinus2) / 2.0;
        }

        $sd = $sd > 0 ? $sd : 1.0;
        $zScore = round(($weight - $median) / $sd, 2);
        $zScore = max(-99.99, min(99.99, $zScore));

        $status = 'normal';
        $label = 'Berat Badan Normal';

        if ($zScore < -3.0) {
            $status = 'severely_underweight';
            $label = 'Berat Badan Sangat Kurang';
        } elseif ($zScore < -2.0) {
            $status = 'underweight';
            $label = 'Berat Badan Kurang';
        } elseif ($zScore <= 1.0) {
            $status = 'normal';
            $label = 'Berat Badan Normal';
        } else {
            $status = 'risk_of_overweight';
            $label = 'Risiko Berat Badan Lebih';
        }

        return [
            'zScore' => $zScore,
            'status' => $status,
            'label' => $label,
        ];
    }

    /**
     * Calculate Weight-for-Height (BB/TB) Z-Score & Status (Wasting indicator)
     */
    public function calculateZScoreBbTb(string $gender, float $height, float $weight): array
    {
        $genderKey = strtolower($gender) === 'female' ? 'female' : 'male';
        // Round height to nearest whole cm clamped between 45 and 120
        $heightRounded = (int) round($height);
        $heightClamped = max(45, min(120, $heightRounded));

        $ref = $this->standards['bb_tb'][$genderKey][(string) $heightClamped] ?? null;

        if (!$ref) {
            return [
                'zScore' => 0.0,
                'status' => 'normal',
                'label' => 'Gizi Baik (Normal)',
            ];
        }

        $median = (float) $ref['median'];
        $sdMinus2 = (float) $ref['sd_minus_2'];
        $sdPlus2 = (float) $ref['sd_plus_2'];

        if ($weight >= $median) {
            $sd = ($sdPlus2 - $median) / 2.0;
        } else {
            $sd = ($median - $sdMinus2) / 2.0;
        }

        $sd = $sd > 0 ? $sd : 1.0;
        $zScore = round(($weight - $median) / $sd, 2);
        $zScore = max(-99.99, min(99.99, $zScore));

        $status = 'normal';
        $label = 'Gizi Baik (Normal)';

        if ($zScore < -3.0) {
            $status = 'severely_wasted';
            $label = 'Gizi Buruk (Severely Wasted)';
        } elseif ($zScore < -2.0) {
            $status = 'wasted';
            $label = 'Gizi Kurang (Wasted)';
        } elseif ($zScore <= 1.0) {
            $status = 'normal';
            $label = 'Gizi Baik (Normal)';
        } elseif ($zScore <= 2.0) {
            $status = 'risk_of_overweight';
            $label = 'Berisiko Gizi Lebih';
        } elseif ($zScore <= 3.0) {
            $status = 'overweight';
            $label = 'Gizi Lebih (Overweight)';
        } else {
            $status = 'obese';
            $label = 'Obesitas';
        }

        return [
            'zScore' => $zScore,
            'status' => $status,
            'label' => $label,
        ];
    }

    /**
     * Comprehensive evaluation of growth and nutritional status.
     * Evaluates Stunting (TB/U), Wasting/Obesity (BB/TB), Underweight (BB/U), and Stagnation (2T).
     *
     * @param array $tbResult Result from calculateZScoreTbU
     * @param array $bbResult Result from calculateZScoreBbU
     * @param array $bbtbResult Result from calculateZScoreBbTb
     * @param bool $isStagnant Stagnation flag (2T)
     * @param float $weight Current weight in kg
     * @param float $height Current height in cm
     * @return array
     */
    public function evaluateOverallStatus(
        array $tbResult,
        array $bbResult,
        array $bbtbResult,
        bool $isStagnant = false,
        float $weight = 0.0,
        float $height = 0.0
    ): array {
        // Biological Plausibility Checks (WHO Anthro extreme flags for balita 0-60 months)
        $isExtreme = false;
        $extremeWarnings = [];

        if ($weight > 35.0) {
            $isExtreme = true;
            $extremeWarnings[] = "Berat badan ({$weight} kg) di atas batas fisiologis wajar balita (>35 kg).";
        } elseif ($weight > 0 && $weight < 1.0) {
            $isExtreme = true;
            $extremeWarnings[] = "Berat badan ({$weight} kg) di bawah batas hidup minimal (<1 kg).";
        }

        if ($height > 130.0) {
            $isExtreme = true;
            $extremeWarnings[] = "Tinggi badan ({$height} cm) di atas batas kurva balita (>130 cm).";
        } elseif ($height > 0 && $height < 40.0) {
            $isExtreme = true;
            $extremeWarnings[] = "Panjang badan ({$height} cm) di bawah batas kurva minimal (<40 cm).";
        }

        if (abs($tbResult['zScore']) >= 6.0 || abs($bbtbResult['zScore']) >= 6.0 || abs($bbResult['zScore']) >= 6.0) {
            $isExtreme = true;
            $extremeWarnings[] = "Z-Score ekstrem (|Z| >= 6.0 SD).";
        }

        // Badge styling for individual indicators
        $tbBadgeClass = match ($tbResult['status']) {
            'severely_stunted' => 'bg-rose-50 text-rose-700 border-rose-200',
            'stunted' => 'bg-amber-50 text-amber-700 border-amber-200',
            'normal' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'tall' => 'bg-sky-50 text-sky-700 border-sky-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };

        $bbtbBadgeClass = match ($bbtbResult['status']) {
            'severely_wasted' => 'bg-rose-50 text-rose-700 border-rose-200',
            'wasted' => 'bg-amber-50 text-amber-700 border-amber-200',
            'normal' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'risk_of_overweight' => 'bg-sky-50 text-sky-700 border-sky-200',
            'overweight' => 'bg-amber-50 text-amber-700 border-amber-200',
            'obese' => 'bg-rose-50 text-rose-700 border-rose-200 font-bold',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };

        $bbBadgeClass = match ($bbResult['status']) {
            'severely_underweight' => 'bg-rose-50 text-rose-700 border-rose-200',
            'underweight' => 'bg-amber-50 text-amber-700 border-amber-200',
            'normal' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'risk_of_overweight' => 'bg-sky-50 text-sky-700 border-sky-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };

        // Determine Overall Risk Level & Badge
        $isDanger = (
            $tbResult['status'] === 'severely_stunted' ||
            $bbtbResult['status'] === 'severely_wasted' ||
            $bbtbResult['status'] === 'obese' ||
            $bbResult['status'] === 'severely_underweight'
        );

        $isWarning = (
            $tbResult['status'] === 'stunted' ||
            $bbtbResult['status'] === 'wasted' ||
            $bbtbResult['status'] === 'overweight' ||
            $bbtbResult['status'] === 'risk_of_overweight' ||
            $bbResult['status'] === 'underweight' ||
            $bbResult['status'] === 'risk_of_overweight' ||
            $isStagnant
        );

        if ($isExtreme) {
            $badgeType = 'red';
            $alertLevel = 'danger';
            $badgeText = 'Input Ekstrem / Periksa Ketikan';
            $recommendation = 'Periksa kembali nilai BB dan TB yang dimasukkan. Angka berada di luar rentang biologis balita normal.';
        } elseif ($isDanger) {
            $badgeType = 'red';
            $alertLevel = 'danger';
            if ($bbtbResult['status'] === 'obese') {
                $badgeText = 'Bahaya: Obesitas Balita (Z > +3 SD)';
                $recommendation = 'Rujuk ke Puskesmas / Dokter Spesialis Anak untuk evaluasi diet dan metabolik anak segera.';
            } elseif ($bbtbResult['status'] === 'severely_wasted') {
                $badgeText = 'Bahaya: Gizi Buruk Akut (Z < -3 SD)';
                $recommendation = 'Segera rujuk ke Puskesmas / RS untuk tata laksana gizi buruk (F-75/F-100 / RUTF).';
            } elseif ($tbResult['status'] === 'severely_stunted') {
                $badgeText = 'Bahaya: Sangat Pendek (Severely Stunted)';
                $recommendation = 'Intervensi gizi intensif dan evaluasi medis Puskesmas untuk defisit pertumbuhan kronis berat.';
            } else {
                $badgeText = 'Bahaya: Berat Badan Sangat Kurang';
                $recommendation = 'Segera konsultasikan dengan tenaga kesehatan untuk evaluasi asupan kalori dan penyakit penyerta.';
            }
        } elseif ($isWarning) {
            $badgeType = 'yellow';
            $alertLevel = 'warning';
            if ($bbtbResult['status'] === 'overweight') {
                $badgeText = 'Waspada: Gizi Lebih';
                $recommendation = 'Atur pola makan gizi seimbang, kurangi konsumsi gula berlebih, dan tingkatkan aktivitas fisik balita.';
            } elseif ($tbResult['status'] === 'stunted') {
                $badgeText = 'Waspada: Terindikasi Stunting';
                $recommendation = 'Prioritaskan konsumsi protein hewani harian (telur, ikan, daging/hati ayam) dan pantau KMS rutin.';
            } elseif ($bbtbResult['status'] === 'wasted') {
                $badgeText = 'Waspada: Gizi Kurang';
                $recommendation = 'Berikan PMT pemulihan kaya protein hewani dan jadwalkan penimbangan ulang 2 minggu lagi.';
            } elseif ($isStagnant) {
                $badgeText = 'Waspada: 2T (BB Stagnan / Turun)';
                $recommendation = 'Berat badan tidak naik dalam 2 kali penimbangan berturut-turut. Rujuk ke Bidan Desa / Puskesmas.';
            } else {
                $badgeText = 'Waspada: Berisiko Masalah Gizi';
                $recommendation = 'Pantau pola asuh makan dan evaluasi grafik pertumbuhan KMS secara berkala.';
            }
        } else {
            $badgeType = 'green';
            $alertLevel = 'normal';
            $badgeText = 'Normal & Gizi Baik';
            $recommendation = 'Pertahankan pola pemberian makan bergizi seimbang dan lanjutkan pemantauan Posyandu setiap bulan.';
        }

        // Combined summary label e.g. "Gizi Baik • Normal" or "Obesitas • Normal"
        $summaryLabel = "{$bbtbResult['label']} • {$tbResult['label']}";

        return [
            'is_extreme' => $isExtreme,
            'extreme_warning' => $isExtreme ? implode(' ', $extremeWarnings) : null,
            'badge_type' => $badgeType,
            'badge_text' => $badgeText,
            'alert_level' => $alertLevel,
            'summary_label' => $summaryLabel,
            'recommendation' => $recommendation,
            'tb_badge_class' => $tbBadgeClass,
            'bbtb_badge_class' => $bbtbBadgeClass,
            'bb_badge_class' => $bbBadgeClass,
        ];
    }

    /**
     * Check Weight Stagnation (2T rule)
     * Checks the latest recorded weight before this entry.
     * If currentWeight <= previousWeight, flag as stagnant (true).
     */
    public function checkWeightStagnation(string $childId, float $currentWeight): bool
    {
        $previousRecord = MeasurementRecord::where('child_id', $childId)
            ->orderBy('measurement_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$previousRecord) {
            return false;
        }

        return $currentWeight <= (float) $previousRecord->weight;
    }

    /**
     * Get KMS Growth Reference Curves for Chart.js
     * $type: 'tb_u' (Height in cm) or 'bb_u' (Weight in kg)
     */
    public function getReferenceCurves(string $gender, string $type = 'tb_u'): array
    {
        $genderKey = strtolower($gender) === 'female' ? 'female' : 'male';
        $table = $this->standards[$type][$genderKey] ?? [];

        $labels = [];
        $plus2SD = [];
        $median = [];
        $minus2SD = [];
        $minus3SD = [];

        for ($m = 0; $m <= 60; $m++) {
            $labels[] = $m;
            $row = $table[(string) $m] ?? null;
            if ($row) {
                $plus2SD[] = (float) $row['sd_plus_2'];
                $median[] = (float) $row['median'];
                $minus2SD[] = (float) $row['sd_minus_2'];
                $minus3SD[] = (float) $row['sd_minus_3'];
            } else {
                $plus2SD[] = null;
                $median[] = null;
                $minus2SD[] = null;
                $minus3SD[] = null;
            }
        }

        return [
            'labels' => $labels,
            'plus2SD' => $plus2SD,
            'median' => $median,
            'minus2SD' => $minus2SD,
            'minus3SD' => $minus3SD,
        ];
    }
}
