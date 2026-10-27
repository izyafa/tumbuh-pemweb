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
