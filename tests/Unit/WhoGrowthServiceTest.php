<?php

namespace Tests\Unit;

use App\Services\WhoGrowthService;
use Carbon\Carbon;
use Tests\TestCase;

class WhoGrowthServiceTest extends TestCase
{
    protected WhoGrowthService $whoService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->whoService = new WhoGrowthService();
    }

    public function test_calculate_age_in_months(): void
    {
        $dob = Carbon::parse('2025-01-01');
        $measDate = Carbon::parse('2026-03-01');

        $months = $this->whoService->calculateAgeInMonths($dob, $measDate);
        $this->assertEquals(14, $months);
    }

    public function test_calculate_zscore_tb_u_normal(): void
    {
        // For a 12-month-old male, median length is 75.7 cm
        $result = $this->whoService->calculateZScoreTbU('male', 12, 75.7);

        $this->assertEquals(0.0, $result['zScore']);
        $this->assertEquals('normal', $result['status']);
    }

    public function test_calculate_zscore_tb_u_stunted(): void
    {
        // Height below -2 SD but above -3 SD (approx 70.0 cm at 12m)
        $result = $this->whoService->calculateZScoreTbU('male', 12, 70.0);

        $this->assertLessThan(-2.0, $result['zScore']);
        $this->assertGreaterThanOrEqual(-3.0, $result['zScore']);
        $this->assertEquals('stunted', $result['status']);
    }

    public function test_calculate_zscore_tb_u_severely_stunted(): void
    {
        // Height below -3 SD (< 68.6 cm at 12m)
        $result = $this->whoService->calculateZScoreTbU('male', 12, 65.0);

        $this->assertLessThan(-3.0, $result['zScore']);
        $this->assertEquals('severely_stunted', $result['status']);
    }

    public function test_calculate_zscore_bb_u(): void
    {
        // For a 12-month-old male, median weight is 9.6 kg
        $result = $this->whoService->calculateZScoreBbU('male', 12, 9.6);

        $this->assertEquals(0.0, $result['zScore']);
        $this->assertEquals('normal', $result['status']);
    }

    public function test_reference_curves_have_61_points(): void
    {
        $curves = $this->whoService->getReferenceCurves('male', 'tb_u');

        $this->assertCount(61, $curves['labels']);
        $this->assertCount(61, $curves['median']);
        $this->assertCount(61, $curves['minus2SD']);
        $this->assertCount(61, $curves['minus3SD']);
        $this->assertCount(61, $curves['plus2SD']);
    }

    public function test_calculate_zscore_bb_tb_obese(): void
    {
        // For a 100 cm male, 100 kg is extreme obesity
        $result = $this->whoService->calculateZScoreBbTb('male', 100.0, 100.0);

        $this->assertEquals('obese', $result['status']);
        $this->assertEquals('Obesitas', $result['label']);
        $this->assertGreaterThan(3.0, $result['zScore']);
    }

    public function test_evaluate_overall_status_flags_extreme_input(): void
    {
        // Input: 100 kg & 100 cm (36 months male)
        $tbResult = $this->whoService->calculateZScoreTbU('male', 36, 100.0);
        $bbResult = $this->whoService->calculateZScoreBbU('male', 36, 100.0);
        $bbtbResult = $this->whoService->calculateZScoreBbTb('male', 100.0, 100.0);

        $eval = $this->whoService->evaluateOverallStatus($tbResult, $bbResult, $bbtbResult, false, 100.0, 100.0);

        $this->assertTrue($eval['is_extreme']);
        $this->assertEquals('red', $eval['badge_type']);
        $this->assertEquals('danger', $eval['alert_level']);
        $this->assertEquals('Input Ekstrem / Periksa Ketikan', $eval['badge_text']);
        $this->assertStringContainsString('Obesitas', $eval['summary_label']);
        $this->assertStringContainsString('Normal', $eval['summary_label']);
    }

    public function test_evaluate_overall_status_identifies_clinical_obesity(): void
    {
        // Realistic obese toddler (24m male, TB 87 cm, BB 16.5 kg, Z = +4.1 SD)
        $tbResult = $this->whoService->calculateZScoreTbU('male', 24, 87.0);
        $bbResult = $this->whoService->calculateZScoreBbU('male', 24, 16.5);
        $bbtbResult = $this->whoService->calculateZScoreBbTb('male', 87.0, 16.5);

        $eval = $this->whoService->evaluateOverallStatus($tbResult, $bbResult, $bbtbResult, false, 16.5, 87.0);

        $this->assertFalse($eval['is_extreme']);
        $this->assertEquals('red', $eval['badge_type']);
        $this->assertEquals('danger', $eval['alert_level']);
        $this->assertEquals('obese', $bbtbResult['status']);
        $this->assertStringContainsString('Obesitas', $eval['badge_text']);
    }

    public function test_evaluate_overall_status_identifies_normal_child(): void
    {
        // Normal 12m male: 75.7 cm, 9.6 kg
        $tbResult = $this->whoService->calculateZScoreTbU('male', 12, 75.7);
        $bbResult = $this->whoService->calculateZScoreBbU('male', 12, 9.6);
        $bbtbResult = $this->whoService->calculateZScoreBbTb('male', 75.7, 9.6);

        $eval = $this->whoService->evaluateOverallStatus($tbResult, $bbResult, $bbtbResult, false, 9.6, 75.7);

        $this->assertFalse($eval['is_extreme']);
        $this->assertEquals('green', $eval['badge_type']);
        $this->assertEquals('normal', $eval['alert_level']);
        $this->assertEquals('Normal & Gizi Baik', $eval['badge_text']);
    }
}
