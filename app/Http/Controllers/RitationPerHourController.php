<?php

namespace App\Http\Controllers;

use App\Models\RitationPerHour;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RitationPerHourController extends Controller
{
    //
    public function index(Request $request)
    {
        if(!empty($request->DATE_REPORT)){
            $date = $request->DATE_REPORT;
        }else{
            $date = Carbon::today()->format('Y-m-d');
        }

        $ritationPerHour = RitationPerHour::where('STATUSENABLED', true)->where('DATE_REPORT', $date)->get();

        $ritationPerHourFocus = DB::connection('focus')->select(
            'SET NOCOUNT ON; EXEC FOCUS_REPORTING.dbo.APP_RATE_PER_HOUR_RESUMEDATA @DATE = ?',
            [$date]
        );
        $ritationPerHourFocus = collect($ritationPerHourFocus);

        $finalRitation = collect();

        foreach ($ritationPerHourFocus as $focus) {
            $code = $focus->CODE;

            $matched = $ritationPerHour->firstWhere('CODE', $code);

            if ($matched) {
                $finalRitation->push([
                    'CODE'   => $focus->CODE,
                    'RANGEHOUR'   => $focus->RANGEJAM,
                    'DATE_REPORT' => $focus->DATE,
                    'TOTAL'       => $focus->N_RATEFMS,
                    'REALTIME'    => $focus->N_RIT_REALTIME,
                    'INFORMATION' => $matched->INFORMATION,
                ]);
            } else {
                $finalRitation->push([
                    'CODE'   => $focus->CODE,
                    'RANGEHOUR'   => $focus->RANGEJAM,
                    'DATE_REPORT' => $focus->DATE,
                    'TOTAL'       => $focus->N_RATEFMS,
                    'REALTIME'    => $focus->N_RIT_REALTIME,
                    'INFORMATION' => null,
                ]);
            }
        }

        return view('ritationPerHour.index', compact('finalRitation'));
    }

    public function edit(Request $request)
    {
        $ids = explode(',', $request->query('ids', ''));

        if (empty($ids) || !is_numeric($ids[0])) {
            return redirect()->back()->with('error', 'Parameter ids tidak valid.');
        }

        $idFirst = $ids[0];
        $date = substr($idFirst, 0, 4) . '-' . substr($idFirst, 4, 2) . '-' . substr($idFirst, 6, 2);

        $ritationPerHour = RitationPerHour::where('STATUSENABLED', true)
            ->where('DATE_REPORT', $date)
            ->get();

        $ritationPerHourFocus = DB::connection('focus')->select(
            'SET NOCOUNT ON; EXEC FOCUS_REPORTING.dbo.APP_RATE_PER_HOUR_RESUMEDATA @DATE = ?',
            [$date]
        );
        $ritationPerHourFocus = collect($ritationPerHourFocus);

        $finalRitation = collect();

        foreach ($ritationPerHourFocus as $focus) {
            $code = $focus->CODE;

            $matched = $ritationPerHour->firstWhere('CODE', $code);

            $finalRitation->push([
                'CODE'        => $focus->CODE,
                'RANGEHOUR'   => $focus->RANGEJAM,
                'DATE_REPORT' => $focus->DATE,
                'TOTAL'       => $focus->N_RATEFMS,
                'REALTIME'    => $focus->N_RIT_REALTIME,
                'INFORMATION' => $matched?->INFORMATION,
            ]);
        }

        $finalRitation = $finalRitation->filter(function ($item) use ($ids) {
            return in_array($item['CODE'], $ids);
        })->values();

        return view('ritationPerHour.edit', compact('finalRitation', 'date'));
    }

    public function update(Request $request)
    {
        $data = $request->input('data');

        if (!$data || !is_array($data)) {
            return back()->with('error', 'Data kosong atau tidak valid.');
        }

        foreach ($data as $item) {
            $code = $item['CODE'] ?? null;
            $information = $item['INFORMATION'] ?? null;

            if (!$code) continue;

            // Ambil date dari 8 digit pertama CODE
            $dateString = substr($code, 0, 8); // contoh: "20250803"
            $dateReport = \Carbon\Carbon::createFromFormat('Ymd', $dateString)->format('Y-m-d');

            $existing = RitationPerHour::where('CODE', $code)
                ->where('DATE_REPORT', $dateReport)
                ->first();

            if ($existing) {
                RitationPerHour::where('ID', $existing->ID)->update([
                    'INFORMATION' => $information,
                    'REPORTING'   => Auth::user()->nrp,
                ]);
            } else {
                // Insert data baru
                RitationPerHour::create([
                    'CODE'          => $code,
                    'DATE_REPORT'   => $dateReport,
                    'INFORMATION'   => $information,
                    'REPORTING'     => Auth::user()->nrp,
                    'STATUSENABLED' => true,
                ]);
            }
        }

        return redirect()->route('ritationPerHour.index')->with('success', 'Informasi ritasi berhasil diupdate');
    }

}
