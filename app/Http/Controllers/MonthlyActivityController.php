<?php

namespace App\Http\Controllers;

use App\Exports\SummaryMonthlyExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MonthlyActivityController extends Controller
{
    //
    public function index(Request $request)
    {
        $bulanTahun = request('DATE_REPORT') ?? Carbon::now()->format('Y-m');

        $carbonBulan = Carbon::createFromFormat('Y-m', $bulanTahun)->startOfMonth();

        $nonMtStart = $carbonBulan->copy()->startOfMonth()->toDateString();
        $nonMtEnd = $carbonBulan->copy()->endOfMonth()->toDateString();

        $action = $request->input('action_type');

        $users = DB::table('users')->pluck('name', 'nrp');

        $convertPIC = function ($picString) use ($users) {
            $nrps = explode(',', $picString);
            $names = [];

            foreach ($nrps as $nrp) {
                $nrp = trim($nrp);
                if (isset($users[$nrp])) {
                    $names[] = $users[$nrp];
                }
            }

            return implode(', ', $names);
        };

        $tower = DB::table('activity_tower as at')
            ->leftJoin('LIST_TOWER as lt', 'at.UUID_TOWER', 'lt.UUID')
            ->leftJoin('users as us', 'at.REPORTING', 'us.nrp')
            ->select(
                'at.UUID',
                'at.STATUSENABLED',
                'at.START',
                'at.FINISH',
                'at.ACTUAL_PROBLEM',
                'at.ACTION_PROBLEM',
                'at.REMARKS',
                'lt.NAMA as NAMA_ITEM',
                DB::raw("CONCAT('(', lt.NAMA, ') ', at.ACTION_PROBLEM, ' (', at.ACTUAL_PROBLEM, ')') as ACTIVITY"),
                'at.ACTION_BY as PIC',
                'us.name as REPORTING',
                'at.DATE_ACTION as DATE_REPORT',
                DB::raw("'Tower' as TEAM")
            )
            ->where('at.STATUSENABLED', true)
            ->whereBetween('at.DATE_ACTION', [$nonMtStart, $nonMtEnd])
            ->get()
            ->map(function ($act) use ($convertPIC) {
                $act->PIC = $convertPIC($act->PIC);
                return $act;
            });

        // --- Ambil data Unit ---
        $unit = DB::table('ACTIVITY_UNIT as un')
            ->leftJoin('LIST_UNIT as lu', 'un.UUID_UNIT', 'lu.UUID')
            ->leftJoin('users as us', 'un.REPORTING', 'us.nrp')
            ->select(
                'un.UUID',
                'un.STATUSENABLED',
                'un.START',
                'un.FINISH',
                'un.ACTUAL_PROBLEM',
                'un.ACTION_PROBLEM',
                'un.REMARKS',
                'lu.VHC_ID as NAMA_ITEM',
                DB::raw("CONCAT('(', lu.VHC_ID, ') ', un.ACTION_PROBLEM, ' (', un.ACTUAL_PROBLEM, ')') as ACTIVITY"),
                'un.ACTION_BY as PIC',
                'us.name as REPORTING',
                'un.DATE_ACTION as DATE_REPORT',
                DB::raw("'Unit' as TEAM")
            )
            ->where('un.STATUSENABLED', true)
            ->whereBetween('un.DATE_ACTION', [$nonMtStart, $nonMtEnd])
            ->get()
            ->map(function ($act) use ($convertPIC) {
                $act->PIC = $convertPIC($act->PIC);
                return $act;
            });

        // --- Ambil data Genset ---
        $genset = DB::table('ACTIVITY_GENSET as gen')
            ->leftJoin('users as us', 'gen.REPORTING', 'us.nrp')
            ->leftJoin('LIST_TOWER as twr', 'gen.UUID_TOWER', 'twr.UUID')
            ->select(
                'gen.UUID',
                'gen.STATUSENABLED',
                'gen.START',
                'gen.FINISH',
                DB::raw("'-' AS ACTUAL_PROBLEM"),
                'gen.KEGIATAN AS ACTION_PROBLEM',
                'gen.REMARKS',
                'twr.NAMA as NAMA_ITEM',
                DB::raw("CONCAT(gen.KEGIATAN, ' ', twr.NAMA, ' (', twr.NO_GENSET, '), Fuel: ', gen.FUEL, '%') as ACTIVITY"),
                'gen.ACTION_BY as PIC',
                'us.name as REPORTING',
                'gen.DATE_REPORT',
                DB::raw("'Tower' as TEAM")
            )
            ->where('gen.STATUSENABLED', true)
            ->whereBetween('gen.DATE_REPORT', [$nonMtStart, $nonMtEnd])
            ->get()
            ->map(function ($act) use ($convertPIC) {
                $act->PIC = $convertPIC($act->PIC);
                return $act;
            });

        $teamOrder = ['All Team', 'Tower', 'Unit'];

        $towerMonthlyActivity = collect()
        ->merge($tower)
        ->merge($genset)
        // ->sortBy(function ($item) use ($teamOrder) {
        //     $teamIndex = array_search($item->TEAM, $teamOrder);
        //     $teamIndex = $teamIndex === false ? PHP_INT_MAX : $teamIndex;
        //     return sprintf('%03d-%s', $teamIndex, $item->START);
        // })
        ->sortBy(function ($item) {
            return \Carbon\Carbon::parse($item->DATE_REPORT);
        })
        ->values();

        $unitMonthlyActivity = collect()
        ->merge($unit)
        // ->sortBy(function ($item) use ($teamOrder) {
        //     $teamIndex = array_search($item->TEAM, $teamOrder);
        //     $teamIndex = $teamIndex === false ? PHP_INT_MAX : $teamIndex;
        //     return sprintf('%03d-%s', $teamIndex, $item->START);
        // })
        ->sortBy(function ($item) {
            return \Carbon\Carbon::parse($item->DATE_REPORT);
        })
        ->values();

        $data = collect()
        ->merge($tower)
        ->merge($genset)
        ->merge($unit)
        ->sortBy(function ($item) use ($teamOrder) {
            $teamIndex = array_search($item->TEAM, $teamOrder);
            $teamIndex = $teamIndex === false ? PHP_INT_MAX : $teamIndex;
            return sprintf('%03d-%s', $teamIndex, $item->START);
        })
        ->values();

        if ($action === 'export') {
        return Excel::download(new SummaryMonthlyExport($towerMonthlyActivity, $unitMonthlyActivity, $nonMtStart, $nonMtEnd), 'Laporan IT-FMS ke GA Bulan ' . Carbon::parse($nonMtStart)->translatedFormat('F Y') .'.xlsx');
        }

        return view('monthlyActivity.index', compact('towerMonthlyActivity','unitMonthlyActivity', 'data'));
    }
}
