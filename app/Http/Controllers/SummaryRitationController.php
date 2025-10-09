<?php

namespace App\Http\Controllers;

use App\Models\RitationPerHour;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SummaryRitationController extends Controller
{
    //
    public function index(Request $request)
    {
        $month = $request->DATE_REPORT ?? Carbon::today()->format('Y-m');

        // Range bulan
        $startOfMonth = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endOfMonth   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        // Ambil master info (untuk kolom INFORMATION)
        $ritationPerHour = RitationPerHour::where('STATUSENABLED', true)
            ->whereBetween('DATE_REPORT', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->get();

        // --- Ambil semua override dalam rentang bulan (ganti koneksi/nama tabel bila perlu)
        // Struktur tabel: DATE_REPORT (date), HOUR tinyint nullable, CODE varchar nullable, OVERRIDE_VALUE int
        $allOverrides = DB::table('NOT_REALTIME_OVERRIDE')
            ->whereBetween('DATE_REPORT', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->get()
            ->groupBy('DATE_REPORT');

        $finalRitation = collect();

        // Loop per hari di bulan
        for ($day = $startOfMonth->copy(); $day->lte($endOfMonth); $day->addDay()) {
            $date = $day->format('Y-m-d');

            // Index override untuk tanggal ini
            $overridesToday = $allOverrides->get($date, collect());
            $overrideByCode = $overridesToday->whereNotNull('CODE')
                ->keyBy(fn($o) => strtoupper(trim($o->CODE)));
            $overrideByHour = $overridesToday->whereNotNull('HOUR')
                ->keyBy('HOUR');

            // Ambil data fokus per hari
            $ritationPerHourFocus = collect(DB::connection('focus')->select(
                'SET NOCOUNT ON; EXEC FOCUS_REPORTING.dbo.APP_RATE_PER_HOUR_RESUMEDATA @DATE = ?',
                [$date]
            ));

            foreach ($ritationPerHourFocus as $focus) {
                // Matching berdasarkan CODE & DATE_REPORT untuk INFORMATION
                $matched = $ritationPerHour->first(function ($item) use ($focus) {
                    return $item->CODE === $focus->CODE
                        && $item->DATE_REPORT === $focus->DATE;
                });

                $rangeHour = $focus->RANGEJAM; // mis. "03:00-03:59"
                [$startHourStr] = explode('-', $rangeHour);
                $hourInt = (int) explode(':', $startHourStr)[0];

                $total        = (int) $focus->N_RATEFMS;
                $realtimeBase = (int) $focus->N_RIT_REALTIME;

                // Default notRealtime dari data asli
                $notRealtime = max(0, $total - $realtimeBase);

                // Terapkan override (prioritas CODE > HOUR, per tanggal)
                $codeKey = strtoupper(trim((string) $focus->CODE));
                if ($overrideByCode->has($codeKey)) {
                    $notRealtime = (int) $overrideByCode[$codeKey]->OVERRIDE_VALUE;
                } elseif ($overrideByHour->has($hourInt)) {
                    $notRealtime = (int) $overrideByHour[$hourInt]->OVERRIDE_VALUE;
                }

                // Validasi
                $notRealtime = max(0, min($notRealtime, $total));
                // Recompute realtime & ACH mengikuti override
                $realtime = $total - $notRealtime;
                $ach = $total > 0 ? ($realtime / $total * 100) : 0.0;

                $finalRitation->push([
                    'CODE'        => $focus->CODE,
                    'RANGEHOUR'   => $rangeHour,
                    'DATE_REPORT' => $focus->DATE,
                    'TOTAL'       => $total,
                    'REALTIME'    => $realtime,     // sudah mengikuti override
                    'NOT_REALTIME'=> $notRealtime,  // akhir (override bila ada)
                    'ACH'         => $ach,
                    'INFORMATION' => $matched->INFORMATION ?? null,
                ]);
            }
        }

        // Kelompokkan per tanggal untuk view
        $groupedByDate = $finalRitation->groupBy('DATE_REPORT');

        return view('summaryRitation.index', [
            'groupedRitation' => $groupedByDate,
            'month'           => $month
        ]);
    }

}
