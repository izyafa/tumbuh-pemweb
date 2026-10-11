<?php

namespace App\Http\Controllers;

use App\Models\Child;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BroadcastController extends Controller
{
    /**
     * Display WhatsApp reminder broadcaster for upcoming Posyandu day
     */
    public function index(Request $request): View
    {
        $children = Child::orderBy('full_name', 'asc')->get();
        $posyanduName = Auth::user()->posyandu_name ?? 'Posyandu Melati RW 03';

        // Default next Posyandu schedule recommendation
        $defaultDate = Carbon::now()->addDays(3)->translatedFormat('l, d F Y');
        $defaultTime = '08.30 - 11.30 WIB';
        $defaultLocation = $posyanduName . ' (Balai Warga RW 03)';

        return view('kader.broadcast.index', compact(
            'children',
            'posyanduName',
            'defaultDate',
            'defaultTime',
            'defaultLocation'
        ));
    }
}
