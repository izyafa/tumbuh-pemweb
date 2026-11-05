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

        $evaluation = $this->whoService->evaluateOverallStatus(
            $tbResult,
            $bbResult,
            $bbtbResult,
            false,
            $weight,
            $height
        );

        return response()->json([
            'age_in_months' => $age,
            'tb_u' => $tbResult,
            'bb_u' => $bbResult,
            'bb_tb' => $bbtbResult,
            'alert_level' => $evaluation['alert_level'],
            'summary_title' => $evaluation['badge_text'],
            'summary_label' => $evaluation['summary_label'],
            'recommendation' => $evaluation['recommendation'],
            'is_extreme' => $evaluation['is_extreme'],
            'extreme_warning' => $evaluation['extreme_warning'],
        ]);
    }
}
