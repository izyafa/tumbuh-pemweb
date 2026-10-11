<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\MeasurementRecord;
use App\Services\WhoGrowthService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MeasurementController extends Controller
{
    protected WhoGrowthService $whoService;

    public function __construct(WhoGrowthService $whoService)
    {
        $this->whoService = $whoService;
    }

    /**
     * Rapid batch measurement entry view for busy Posyandu days
     */
    public function batch(): View
    {
        $children = Child::orderBy('full_name', 'asc')->get(['id', 'nik', 'full_name', 'gender', 'date_of_birth']);

        return view('kader.measurements.batch', compact('children'));
    }

    /**
     * Store multiple measurement records in a single batch
     */
    public function storeBatch(Request $request): RedirectResponse
    {
        $request->validate([
            'measurement_date' => ['required', 'date'],
            'measurements' => ['required', 'array', 'min:1'],
            'measurements.*.child_id' => ['required', 'exists:children,id'],
            'measurements.*.weight' => ['required', 'numeric', 'min:1', 'max:50'],
            'measurements.*.height' => ['required', 'numeric', 'min:30', 'max:150'],
            'measurements.*.head_circum' => ['nullable', 'numeric', 'min:20', 'max:70'],
            'measurements.*.arm_circum' => ['nullable', 'numeric', 'min:5', 'max:35'],
            'measurements.*.notes' => ['nullable', 'string', 'max:500'],
        ]);

        $measurementDate = Carbon::parse($request->input('measurement_date'));
        $measuredById = Auth::id();
        $savedCount = 0;

        DB::transaction(function () use ($request, $measurementDate, $measuredById, &$savedCount) {
            foreach ($request->input('measurements') as $row) {
                if (empty($row['child_id']) || empty($row['weight']) || empty($row['height'])) {
                    continue;
                }

                $child = Child::findOrFail($row['child_id']);
                $ageInMonths = $this->whoService->calculateAgeInMonths($child->date_of_birth, $measurementDate);

                $weight = (float) $row['weight'];
                $height = (float) $row['height'];
                $headCircum = !empty($row['head_circum']) ? (float) $row['head_circum'] : null;
                $armCircum = !empty($row['arm_circum']) ? (float) $row['arm_circum'] : null;

                $tbResult = $this->whoService->calculateZScoreTbU($child->gender, $ageInMonths, $height);
                $bbResult = $this->whoService->calculateZScoreBbU($child->gender, $ageInMonths, $weight);
                $bbtbResult = $this->whoService->calculateZScoreBbTb($child->gender, $height, $weight);
                $isStagnant = $this->whoService->checkWeightStagnation($child->id, $weight);

                MeasurementRecord::create([
                    'child_id' => $child->id,
                    'measured_by_id' => $measuredById,
                    'measurement_date' => $measurementDate,
                    'age_in_months' => $ageInMonths,
                    'weight' => $weight,
                    'height' => $height,
                    'head_circum' => $headCircum,
                    'arm_circum' => $armCircum,
                    'zscore_tb_u' => $tbResult['zScore'],
                    'zscore_bb_u' => $bbResult['zScore'],
                    'zscore_bb_tb' => $bbtbResult['zScore'],
                    'stunting_status' => $tbResult['status'],
                    'is_weight_stagnant' => $isStagnant,
                    'notes' => $row['notes'] ?? null,
                ]);

                $savedCount++;
            }
        });

        return redirect()->route('kader.dashboard')
            ->with('success', "Berhasil menyimpan {$savedCount} data penimbangan balita secara batch.");
    }

    /**
     * Store a single measurement entry from child profile
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'child_id' => ['required', 'exists:children,id'],
            'measurement_date' => ['required', 'date'],
            'weight' => ['required', 'numeric', 'min:1', 'max:50'],
            'height' => ['required', 'numeric', 'min:30', 'max:150'],
            'head_circum' => ['nullable', 'numeric', 'min:20', 'max:70'],
            'arm_circum' => ['nullable', 'numeric', 'min:5', 'max:35'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $child = Child::findOrFail($validated['child_id']);
        $measurementDate = Carbon::parse($validated['measurement_date']);
        $ageInMonths = $this->whoService->calculateAgeInMonths($child->date_of_birth, $measurementDate);

        $weight = (float) $validated['weight'];
        $height = (float) $validated['height'];

        $tbResult = $this->whoService->calculateZScoreTbU($child->gender, $ageInMonths, $height);
        $bbResult = $this->whoService->calculateZScoreBbU($child->gender, $ageInMonths, $weight);
        $bbtbResult = $this->whoService->calculateZScoreBbTb($child->gender, $height, $weight);
        $isStagnant = $this->whoService->checkWeightStagnation($child->id, $weight);

        MeasurementRecord::create([
            'child_id' => $child->id,
            'measured_by_id' => Auth::id(),
            'measurement_date' => $measurementDate,
            'age_in_months' => $ageInMonths,
            'weight' => $weight,
            'height' => $height,
            'head_circum' => $validated['head_circum'] ?? null,
            'arm_circum' => $validated['arm_circum'] ?? null,
            'zscore_tb_u' => $tbResult['zScore'],
            'zscore_bb_u' => $bbResult['zScore'],
            'zscore_bb_tb' => $bbtbResult['zScore'],
            'stunting_status' => $tbResult['status'],
            'is_weight_stagnant' => $isStagnant,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('kader.children.show', $child->id)
            ->with('success', 'Data pengukuran baru berhasil dicatat.');
    }

    /**
     * AJAX endpoint for immediate calculation preview
     */
    public function preview(Request $request): JsonResponse
    {
        $childId = $request->input('child_id');
        $weight = (float) $request->input('weight');
        $height = (float) $request->input('height');
        $dateStr = $request->input('measurement_date', Carbon::today()->toDateString());

        if (!$childId || $weight <= 0 || $height <= 0) {
            return response()->json(['error' => 'Input tidak lengkap'], 422);
        }

        $child = Child::find($childId);
        if (!$child) {
            return response()->json(['error' => 'Anak tidak ditemukan'], 404);
        }

        $measurementDate = Carbon::parse($dateStr);
        $ageInMonths = $this->whoService->calculateAgeInMonths($child->date_of_birth, $measurementDate);

        $tbResult = $this->whoService->calculateZScoreTbU($child->gender, $ageInMonths, $height);
        $bbResult = $this->whoService->calculateZScoreBbU($child->gender, $ageInMonths, $weight);
        $bbtbResult = $this->whoService->calculateZScoreBbTb($child->gender, $height, $weight);
        $isStagnant = $this->whoService->checkWeightStagnation($child->id, $weight);

        // Badge level: green (Normal), yellow (Waspada/Risiko), red (Indikasi Masalah Gizi)
        $badgeType = 'green';
        $badgeText = 'Normal';

        if ($tbResult['status'] === 'severely_stunted' || $bbtbResult['status'] === 'severely_wasted' || $bbResult['status'] === 'severely_underweight') {
            $badgeType = 'red';
            $badgeText = 'Merah (Bahaya / Masalah Akut)';
        } elseif ($tbResult['status'] === 'stunted' || $bbtbResult['status'] === 'wasted' || $bbResult['status'] === 'underweight' || $isStagnant) {
            $badgeType = 'yellow';
            $badgeText = 'Kuning (Waspada / Risiko)';
        }

        return response()->json([
            'age_in_months' => $ageInMonths,
            'tb_u' => $tbResult,
            'bb_u' => $bbResult,
            'bb_tb' => $bbtbResult,
            'is_weight_stagnant' => $isStagnant,
            'badge_type' => $badgeType,
            'badge_text' => $badgeText,
        ]);
    }

    public function destroy(string $id): RedirectResponse
    {
        $record = MeasurementRecord::findOrFail($id);
        $childId = $record->child_id;
        $record->delete();

        return redirect()->route('kader.children.show', $childId)
            ->with('success', 'Catatan pengukuran berhasil dihapus.');
    }
}
