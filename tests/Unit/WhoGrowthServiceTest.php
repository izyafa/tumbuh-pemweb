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
}
