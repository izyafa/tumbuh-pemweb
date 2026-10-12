<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Services\AiNutritionService;
use App\Services\WhoGrowthService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentPortalController extends Controller
{
    protected WhoGrowthService $whoService;
    protected AiNutritionService $aiService;

    public function __construct(WhoGrowthService $whoService, AiNutritionService $aiService)
    {
        $this->whoService = $whoService;
        $this->aiService = $aiService;
    }

    public function show(string $token): View
    {
        $child = Child::with(['measurementRecords' => function ($q) {
            $q->orderBy('measurement_date', 'asc');
        }, 'aiConsultations' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }, 'immunizationRecords' => function ($q) {
            $q->orderBy('administered_date', 'asc');
        }])->where('public_token', $token)->firstOrFail();

        $latestMeasurement = $child->latestMeasurement;
        $latestConsultation = $child->aiConsultations->first();

        // Build KMS reference curves
        $tbuCurves = $this->whoService->getReferenceCurves($child->gender, 'tb_u');
        $bbuCurves = $this->whoService->getReferenceCurves($child->gender, 'bb_u');

        // Build child measured series for 0-60 months
        $childHeightPoints = array_fill(0, 61, null);
        $childWeightPoints = array_fill(0, 61, null);

        foreach ($child->measurementRecords as $rec) {
            $age = min(60, max(0, (int) $rec->age_in_months));
            $childHeightPoints[$age] = (float) $rec->height;
            $childWeightPoints[$age] = (float) $rec->weight;
        }

        // WhatsApp Share Text
        $publicUrl = route('parent.portal', ['token' => $child->public_token]);
        $waText = "Halo! Berikut hasil pemantauan gizi & tumbuh kembang ananda *{$child->full_name}* di Posyandu:\n\n";
        if ($latestMeasurement) {
            $tgl = $latestMeasurement->measurement_date->translatedFormat('d F Y');
            $waText .= "📅 Tanggal: {$tgl}\n";
            $waText .= "👶 Usia: {$latestMeasurement->age_in_months} Bulan\n";
            $waText .= "⚖️ Berat Badan: {$latestMeasurement->weight} kg\n";
            $waText .= "📏 Tinggi Badan: {$latestMeasurement->height} cm\n";
            $waText .= "📊 Status Pertumbuhan: {$latestMeasurement->stunting_status_label}\n";
            $waText .= "📈 Tren Kenaikan BB: " . ($latestMeasurement->is_weight_stagnant ? '⚠️ Perlu Perhatian (BB Stagnan)' : '✅ Kenaikan Baik') . "\n\n";
        }
        $waText .= "Lihat grafik digital KMS & panduan menu gizi harian secara lengkap di:\n{$publicUrl}\n\n";
        $waText .= "Diberdayakan oleh Platform Tumbuh — Kolaborasi Posyandu";

        $waShareUrl = "https://api.whatsapp.com/send?text=" . urlencode($waText);

        return view('parent.portal', compact(
            'child',
            'latestMeasurement',
            'latestConsultation',
            'tbuCurves',
            'bbuCurves',
            'childHeightPoints',
            'childWeightPoints',
            'waText',
            'waShareUrl',
            'publicUrl'
        ));
    }

    /**
     * Printable KMS card for parents without login
     */
    public function printKms(string $token): View
    {
        $child = Child::with([
            'measurementRecords' => function ($q) {
                $q->orderBy('measurement_date', 'asc');
            },
            'immunizationRecords' => function ($q) {
                $q->orderBy('administered_date', 'asc');
            },
        ])->where('public_token', $token)->firstOrFail();

        $latestMeasurement = $child->latestMeasurement;

        return view('kader.children.kms_print', compact('child', 'latestMeasurement'));
    }

    /**
     * Dedicated AI Nutrition & Meal Recommendation page for parents
     */
    public function menuGizi(string $token): View
    {
        $child = Child::with(['latestMeasurement', 'aiConsultations' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }])->where('public_token', $token)->firstOrFail();

        $latestMeasurement = $child->latestMeasurement;
        $latestConsultation = $child->aiConsultations->first();

        $parentPortalUrl = route('parent.portal', ['token' => $child->public_token]);

        return view('parent.menu_gizi', compact(
            'child',
            'latestMeasurement',
            'latestConsultation',
            'parentPortalUrl'
        ));
    }

    /**
     * Generate dynamic AI nutrition consultation from parent portal
     */
    public function generateAiConsult(Request $request, string $token): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'daily_budget' => ['required', 'integer', 'min:5000', 'max:2000000'],
        ]);

        $child = Child::with('latestMeasurement')->where('public_token', $token)->firstOrFail();
        $latest = $child->latestMeasurement;

        if (!$latest) {
            $errorMsg = 'Belum ada data penimbangan balita dari Posyandu. Silakan lakukan penimbangan di Posyandu terlebih dahulu.';
            if ($request->expectsJson()) {
                return response()->json(['error' => $errorMsg], 422);
            }
            return back()->with('error', $errorMsg);
        }

        $dailyBudget = (int) $request->input('daily_budget', 50000);

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

        $promptSummary = "Konsultasi mandiri orang tua untuk {$child->full_name} ({$latest->age_in_months} bln), TB {$latest->height}cm, BB {$latest->weight}kg, Budget: Rp" . number_format($dailyBudget, 0, ',', '.');

        $consultation = \App\Models\AiConsultation::create([
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

        return redirect()->route('parent.menu-gizi', $token)
            ->with('success', 'Rekomendasi menu baru berhasil diracik oleh AI!');
    }
}
