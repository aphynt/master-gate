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
        $date = $request->DATE_REPORT ?: Carbon::today()->format('Y-m-d');

        // Data utama
        $ritationPerHour = RitationPerHour::where('STATUSENABLED', true)
            ->where('DATE_REPORT', $date)
            ->get();

        $ritationPerHourFocus = DB::connection('focus')->select(
            'SET NOCOUNT ON; EXEC FOCUS_REPORTING.dbo.APP_RATE_PER_HOUR_RESUMEDATA @DATE = ?',
            [$date]
        );

        $statusPerHour = DB::connection('focus_reporting')
            ->table('PERIODIC.MODUS_STATUS_PER_HOUR as A')
            ->leftJoin('focus.dbo.FLT_VSASTATUS as B', 'A.STATUS_MODUS', '=', 'B.VSA_STATUSID')
            ->select(
                'A.ID','A.DATE','A.HOUR','A.VHC_TYPE','A.VHC_ID',
                'A.STATUS_MODUS','B.VSA_STATUSDESC','A.DURATION','A.CREATED_AT'
            )
            ->where('A.DATE', $date)
            ->orderByDesc('A.DATE')
            ->get();

        $ritationPerHourFocus = collect($ritationPerHourFocus);

        // Ambil override (ganti koneksi/nama tabel sesuai DB bos)
        $overrides = DB::table('NOT_REALTIME_OVERRIDE')
            ->where('DATE_REPORT', $date)
            ->get();

        $overrideByCode = $overrides->whereNotNull('CODE')
            ->keyBy(fn($o) => strtoupper(trim($o->CODE)));
        $overrideByHour = $overrides->whereNotNull('HOUR')
            ->keyBy('HOUR');

        // Agregat
        $sumTotal = $sumRealtime = $sumNotRealtime = 0;
        $sumTotalSiang = $sumRealtimeSiang = $sumNotRealtimeSiang = 0;
        $sumTotalMalam = $sumRealtimeMalam = $sumNotRealtimeMalam = 0;

        $finalRitation = collect();

        foreach ($ritationPerHourFocus as $focus) {
            $code      = $focus->CODE;
            $rangeHour = $focus->RANGEJAM; // "07:00-07:59"
            $matched   = $ritationPerHour->firstWhere('CODE', $code);

            [$startHourStr] = explode('-', $rangeHour);
            $hourInt = (int) explode(':', $startHourStr)[0];

            // Dominan status per jam
            $statusGroup = $statusPerHour->where('HOUR', $hourInt);
            $dominant = $statusGroup
                ->groupBy('VSA_STATUSDESC')
                ->map(fn($items) => $items->sum('DURATION'))
                ->sortDesc()
                ->keys()
                ->first();

            // Nilai dasar
            $total        = (int) $focus->N_RATEFMS;
            $realtimeBase = (int) $focus->N_RIT_REALTIME;

            // Default notRealtime dari data base
            $notRealtime = max(0, $total - $realtimeBase);

            // Cek override (prioritas CODE > HOUR)
            $codeKey = strtoupper(trim((string) $code));
            if ($overrideByCode->has($codeKey)) {
                $notRealtime = (int) $overrideByCode[$codeKey]->OVERRIDE_VALUE;
            } elseif ($overrideByHour->has($hourInt)) {
                $notRealtime = (int) $overrideByHour[$hourInt]->OVERRIDE_VALUE;
            }

            // Clamp agar valid
            $notRealtime = max(0, min($notRealtime, $total));
            // Recompute realtime mengikuti override
            $realtime = $total - $notRealtime;

            // ACH ikut realtime hasil penyesuaian
            $ach = $total > 0 ? ($realtime / $total * 100) : 0.0;

            // Style flags
            $rowHighlight = $notRealtime >= 10;
            $achWarn      = ($ach > 0 && $ach < 95.0);

            // Agregat pakai nilai yang SUDAH disesuaikan
            $sumTotal      += $total;
            $sumRealtime   += $realtime;
            $sumNotRealtime+= $notRealtime;

            if ($hourInt >= 7 && $hourInt <= 18) {
                $sumTotalSiang       += $total;
                $sumRealtimeSiang    += $realtime;
                $sumNotRealtimeSiang += $notRealtime;
            } else {
                $sumTotalMalam       += $total;
                $sumRealtimeMalam    += $realtime;
                $sumNotRealtimeMalam += $notRealtime;
            }

            $finalRitation->push([
                'CODE'             => $code,
                'RANGEHOUR'        => $rangeHour,
                'DATE_REPORT'      => $focus->DATE,
                'TOTAL'            => $total,
                'REALTIME'         => $realtime,       // <— realtime sudah mengikuti override
                'NOT_REALTIME'     => $notRealtime,    // <— nilai akhir (override kalau ada)
                'ACH'              => $ach,
                'INFORMATION'      => $matched->INFORMATION ?? null,
                'STATUS_PRODUKSI'  => $dominant,
                'row_highlight'    => $rowHighlight,
                'ach_warn'         => $achWarn,
            ]);
        }

        $totals = [
            'sumTotal'               => $sumTotal,
            'sumRealtime'            => $sumRealtime,
            'sumNotRealtime'         => $sumNotRealtime,
            'sumTotalSiang'          => $sumTotalSiang,
            'sumRealtimeSiang'       => $sumRealtimeSiang,
            'sumNotRealtimeSiang'    => $sumNotRealtimeSiang,
            'sumTotalMalam'          => $sumTotalMalam,
            'sumRealtimeMalam'       => $sumRealtimeMalam,
            'sumNotRealtimeMalam'    => $sumNotRealtimeMalam,
        ];

        return view('ritationPerHour.index', compact('finalRitation', 'totals', 'date'));
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
