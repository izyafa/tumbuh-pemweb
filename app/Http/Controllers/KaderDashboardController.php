<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\MeasurementRecord;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KaderDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $totalChildren = Child::count();

        // Get latest measurements
        $latestRecordIds = MeasurementRecord::selectRaw('max(id) as id')
            ->groupBy('child_id')
            ->pluck('id');

        $latestRecords = MeasurementRecord::with('child')
            ->whereIn('id', $latestRecordIds)
            ->get();

        $stuntedCount = $latestRecords->whereIn('stunting_status', ['stunted', 'severely_stunted'])->count();
        $normalCount = $latestRecords->where('stunting_status', 'normal')->count();
        $tallCount = $latestRecords->where('stunting_status', 'tall')->count();
        $stagnantCount = $latestRecords->where('is_weight_stagnant', true)->count();
        $wastedCount = $latestRecords->where('zscore_bb_tb', '<', -2.0)->count();

        $stuntingRate = $totalChildren > 0 ? round(($stuntedCount / $totalChildren) * 100, 1) : 0;

        // Urgent attention children (stunting or stagnant)
        $urgentChildren = Child::whereHas('latestMeasurement', function ($q) {
            $q->whereIn('stunting_status', ['severely_stunted', 'stunted'])
              ->orWhere('is_weight_stagnant', true);
        })
        ->with('latestMeasurement')
        ->limit(6)
        ->get();

        // Recent measurement activity
        $recentMeasurements = MeasurementRecord::with(['child', 'measuredBy'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('kader.dashboard', compact(
            'totalChildren',
            'stuntedCount',
            'normalCount',
            'tallCount',
            'stagnantCount',
            'wastedCount',
            'stuntingRate',
            'urgentChildren',
            'recentMeasurements'
        ));
    }
}
