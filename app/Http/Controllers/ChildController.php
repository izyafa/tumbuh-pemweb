<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Services\WhoGrowthService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ChildController extends Controller
{
    protected WhoGrowthService $whoService;

    public function __construct(WhoGrowthService $whoService)
    {
        $this->whoService = $whoService;
    }

    public function index(Request $request): View
    {
        $query = Child::with('latestMeasurement');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'ilike', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('parent_name', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->input('gender'));
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            $query->whereHas('latestMeasurement', function ($q) use ($status) {
                $q->where('stunting_status', $status);
            });
        }

        $children = $query->orderBy('full_name', 'asc')->paginate(10)->withQueryString();

        return view('kader.children.index', compact('children'));
    }

    public function create(): View
    {
        return view('kader.children.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'size:16', 'unique:children,nik'],
            'full_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['required', 'in:male,female'],
            'birth_weight' => ['required', 'numeric', 'min:0.5', 'max:10'],
            'birth_height' => ['required', 'numeric', 'min:20', 'max:70'],
            'parent_name' => ['required', 'string', 'max:255'],
            'parent_phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
        ]);

        $validated['public_token'] = (string) Str::uuid();

        $child = Child::create($validated);

        return redirect()->route('kader.children.show', $child->id)
            ->with('success', "Data anak {$child->full_name} berhasil didaftarkan.");
    }

    public function show(string $id): View
    {
        $child = Child::with([
            'measurementRecords.measuredBy',
            'immunizationRecords.recordedBy',
            'aiConsultations' => function ($q) {
                $q->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);

        $latestMeasurement = $child->latestMeasurement;

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

        // Prepare WhatsApp message string
        $publicUrl = route('parent.portal', ['token' => $child->public_token]);
        $phoneClean = preg_replace('/[^0-9]/', '', $child->parent_phone);
        if (str_starts_with($phoneClean, '0')) {
            $phoneClean = '62' . substr($phoneClean, 1);
        }

        $waText = "Halo Bapak/Ibu {$child->parent_name},\n\n";
        $waText .= "Berikut ringkasan hasil pengukuran tumbuh kembang anak *{$child->full_name}* di Posyandu:\n";
        if ($latestMeasurement) {
            $tgl = $latestMeasurement->measurement_date->translatedFormat('d F Y');
            $waText .= "• Tanggal Pemeriksaan: {$tgl}\n";
            $waText .= "• Usia: {$latestMeasurement->age_in_months} Bulan\n";
            $waText .= "• Berat Badan: {$latestMeasurement->weight} kg\n";
            $waText .= "• Tinggi Badan: {$latestMeasurement->height} cm\n";
            $waText .= "• Status TB/U: {$latestMeasurement->stunting_status_label} (Z-Score: {$latestMeasurement->zscore_tb_u})\n";
            $waText .= "• Status BB: " . ($latestMeasurement->is_weight_stagnant ? '⚠️ Berat Badan Stagnan (Tidak Naik)' : '✅ Kenaikan BB Normal') . "\n\n";
        } else {
            $waText .= "Data balita telah terdaftar di sistem Tumbuh.\n\n";
        }
        $waText .= "Pantau grafik kurva pertumbuhan digital KMS & konsultasi gizi harian secara lengkap tanpa login melalui tautan resmi:\n{$publicUrl}\n\n";
        $waText .= "Mari bersama cegah stunting demi masa depan buah hati!";

        $waShareUrl = "https://api.whatsapp.com/send?phone={$phoneClean}&text=" . urlencode($waText);

        return view('kader.children.show', compact(
            'child',
            'latestMeasurement',
            'tbuCurves',
            'bbuCurves',
            'childHeightPoints',
            'childWeightPoints',
            'waText',
            'waShareUrl'
        ));
    }

    /**
     * Printable KMS card view for printing to physical paper or PDF
     */
    public function printKms(string $id): View
    {
        $child = Child::with([
            'measurementRecords' => function ($q) {
                $q->orderBy('measurement_date', 'asc');
            },
            'immunizationRecords' => function ($q) {
                $q->orderBy('administered_date', 'asc');
            },
        ])->findOrFail($id);

        $latestMeasurement = $child->latestMeasurement;

        return view('kader.children.kms_print', compact('child', 'latestMeasurement'));
    }

    public function edit(string $id): View
    {
        $child = Child::findOrFail($id);
        return view('kader.children.edit', compact('child'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $child = Child::findOrFail($id);

        $validated = $request->validate([
            'nik' => ['required', 'string', 'size:16', "unique:children,nik,{$child->id}"],
            'full_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['required', 'in:male,female'],
            'birth_weight' => ['required', 'numeric', 'min:0.5', 'max:10'],
            'birth_height' => ['required', 'numeric', 'min:20', 'max:70'],
            'parent_name' => ['required', 'string', 'max:255'],
            'parent_phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
        ]);

        $child->update($validated);

        return redirect()->route('kader.children.show', $child->id)
            ->with('success', 'Profil balita berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $child = Child::findOrFail($id);
        $name = $child->full_name;
        $child->delete();

        return redirect()->route('kader.children.index')
            ->with('success', "Data anak {$name} berhasil dihapus.");
    }
}
