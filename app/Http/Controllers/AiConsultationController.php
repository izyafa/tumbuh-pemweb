<?php

namespace App\Http\Controllers;

use App\Models\AiConsultation;
use App\Models\Child;
use App\Services\AiNutritionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiConsultationController extends Controller
{
    protected AiNutritionService $aiService;

    public function __construct(AiNutritionService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Dedicated AI Clinical Nutrition Consultation page for Cadres and Admin
     */
    public function show(string $childId): View
    {
        $child = Child::with([
            'latestMeasurement',
            'aiConsultations' => function ($q) {
                $q->orderBy('created_at', 'desc');
            }
        ])->findOrFail($childId);

        $latestMeasurement = $child->latestMeasurement;
        $latestConsultation = $child->aiConsultations->first();

        return view('kader.children.ai_consult', compact(
            'child',
            'latestMeasurement',
            'latestConsultation'
        ));
    }

    public function generate(Request $request, string $childId): JsonResponse|RedirectResponse
    {
        $request->validate([
            'daily_budget' => ['required', 'integer', 'min:5000', 'max:2000000'],
        ]);

        $child = Child::with('latestMeasurement')->findOrFail($childId);
        $latest = $child->latestMeasurement;

        if (!$latest) {
            $errorMsg = 'Belum ada data penimbangan untuk balita ini. Silakan input pengukuran terlebih dahulu.';
            if ($request->expectsJson()) {
                return response()->json(['error' => $errorMsg], 422);
            }
            return back()->with('error', $errorMsg);
        }

        $dailyBudget = (int) $request->input('daily_budget', 20000);

        $aiResult = $this->aiService->generateConsultation(
            $child->full_name,
            $latest->age_in_months,
            $child->gender,
            $latest->weight,
            $latest->height,
            $latest->zscore_tb_u,
            $latest->zscore_bb_u,
            $latest->zscore_bb_tb,
            $latest->is_weight_stagnant,
            $dailyBudget
        );

        $promptSummary = "Konsultasi gizi untuk {$child->full_name} ({$latest->age_in_months} bln), TB {$latest->height}cm, BB {$latest->weight}kg, Status: {$latest->stunting_status}, Budget: Rp" . number_format($dailyBudget, 0, ',', '.');

        $consultation = AiConsultation::create([
            'child_id' => $child->id,
            'daily_budget' => $dailyBudget,
            'prompt_summary' => $promptSummary,
            'ai_response' => $aiResult,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'consultation' => $consultation,
                'ai_response' => $aiResult,
            ]);
        }

        return redirect()->route('kader.children.ai-consult.show', $child->id)
            ->with('success', 'Rekomendasi Menu & Tindakan Klinis AI berhasil digenerate.');
    }
}
