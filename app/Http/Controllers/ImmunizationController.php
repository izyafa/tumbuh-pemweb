<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\ImmunizationRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImmunizationController extends Controller
{
    /**
     * Store an immunization or supplementation record for a child.
     */
    public function store(Request $request, string $childId): RedirectResponse
    {
        $child = Child::findOrFail($childId);

        $validated = $request->validate([
            'vaccine_name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'in:imunisasi_dasar,vitamin_a,obat_cacing'],
            'administered_date' => ['required', 'date', 'before_or_equal:today'],
            'batch_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['child_id'] = $child->id;
        $validated['recorded_by_id'] = Auth::id();

        ImmunizationRecord::create($validated);

        return back()->with('success', "Pencatatan {$validated['vaccine_name']} untuk {$child->full_name} berhasil disimpan.");
    }

    /**
     * Delete an immunization record.
     */
    public function destroy(string $id): RedirectResponse
    {
        $record = ImmunizationRecord::findOrFail($id);
        $vaccineName = $record->vaccine_name;
        $record->delete();

        return back()->with('success', "Catatan {$vaccineName} berhasil dihapus.");
    }
}
