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

        $datesInMonth = collect();
        $startOfMonth = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endOfMonth   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $datesInMonth->push($date->format('Y-m-d'));
        }

        $ritationPerHour = RitationPerHour::where('STATUSENABLED', true)
            ->whereBetween('DATE_REPORT', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->get();

        $finalRitation = collect();

        foreach ($datesInMonth as $date) {
            $ritationPerHourFocus = DB::connection('focus')->select(
                'SET NOCOUNT ON; EXEC FOCUS_REPORTING.dbo.APP_RATE_PER_HOUR_RESUMEDATA @DATE = ?',
                [$date]
            );
            $ritationPerHourFocus = collect($ritationPerHourFocus);

            foreach ($ritationPerHourFocus as $focus) {
                // Matching berdasarkan CODE dan DATE_REPORT
                $matched = $ritationPerHour->first(function ($item) use ($focus) {
                    return $item->CODE === $focus->CODE
                        && $item->DATE_REPORT === $focus->DATE;
                });

                $finalRitation->push([
                    'CODE'        => $focus->CODE,
                    'RANGEHOUR'   => $focus->RANGEJAM,
                    'DATE_REPORT' => $focus->DATE,
                    'TOTAL'       => $focus->N_RATEFMS,
                    'REALTIME'    => $focus->N_RIT_REALTIME,
                    'INFORMATION' => $matched->INFORMATION ?? null,
                ]);
            }
        }

        $groupedByDate = $finalRitation->groupBy('DATE_REPORT');

        return view('summaryRitation.index', [
            'groupedRitation' => $groupedByDate,
            'month'           => $month
        ]);
    }

}
