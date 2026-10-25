<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\EducationalVideo;
use App\Models\MeasurementRecord;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $totalChildren = Child::count();
        $totalKaders = User::where('role', 'kader')->count();
        $totalMeasurements = MeasurementRecord::count();
        $totalVideos = EducationalVideo::count();

        // Latest status distribution
        $latestRecordIds = MeasurementRecord::selectRaw('max(id) as id')
            ->groupBy('child_id')
            ->pluck('id');

        $latestRecords = MeasurementRecord::whereIn('id', $latestRecordIds)->get();

        $severelyStunted = $latestRecords->where('stunting_status', 'severely_stunted')->count();
        $stunted = $latestRecords->where('stunting_status', 'stunted')->count();
        $normal = $latestRecords->where('stunting_status', 'normal')->count();
        $tall = $latestRecords->where('stunting_status', 'tall')->count();

        $stuntingRate = $totalChildren > 0 ? round((($severelyStunted + $stunted) / $totalChildren) * 100, 1) : 0;
        $stagnantWeightCount = $latestRecords->where('is_weight_stagnant', true)->count();
        $wastingCount = $latestRecords->where('zscore_bb_tb', '<', -2.0)->count();

        $recentRecords = MeasurementRecord::with(['child', 'measuredBy'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalChildren',
            'totalKaders',
            'totalMeasurements',
            'totalVideos',
            'severelyStunted',
            'stunted',
            'normal',
            'tall',
            'stuntingRate',
            'stagnantWeightCount',
            'wastingCount',
            'recentRecords'
        ));
    }

    public function users(): View
    {
        $users = User::orderBy('role', 'asc')->orderBy('name', 'asc')->paginate(10);
        return view('admin.users', compact('users'));
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,kader'],
            'posyandu_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users')
            ->with('success', "Pengguna {$validated['name']} berhasil ditambahkan.");
    }

    public function updateUser(Request $request, string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', "unique:users,email,{$user->id}"],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'in:admin,kader'],
            'posyandu_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users')
            ->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function destroyUser(string $id): RedirectResponse
    {
        if (Auth::id() == $id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user = User::findOrFail($id);
        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', "Pengguna {$name} telah dihapus.");
    }

    public function videos(): View
    {
        $videos = EducationalVideo::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.videos', compact('videos'));
    }
}
