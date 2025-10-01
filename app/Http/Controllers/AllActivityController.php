<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AllActivityController extends Controller
{
    //
    public function index()
    {
        return view('allActivity.index');
    }

    public function api(Request $request)
    {

        $start = $request->input('start', 0);
        $length = $request->input('length', 20);
        $draw = $request->input('draw');
        $search = $request->input('search.value');

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

        $additional = DB::table('ACTIVITY_ADDITIONAL as add')
            ->leftJoin('LIST_TEAM as team', 'add.UUID_TEAM', 'team.UUID')
            ->leftJoin('users as us', 'add.REPORTING', 'us.nrp')
            ->select(
                'add.UUID',
                'add.STATUSENABLED',
                'add.START',
                'add.FINISH',
                'add.ACTION_PROBLEM as ACTIVITY',
                'add.ACTION_BY as PIC',
                'us.name as REPORTING',
                'add.DATE_REPORT',
                'team.NAMA as TEAM'
            )
            ->where('add.STATUSENABLED', true)
            // ->where('add.DATE_REPORT', $date)
            ->get()
            ->map(function ($act) use ($convertPIC) {
                $act->PIC = $convertPIC($act->PIC);
                return $act;
            });

        // --- Ambil data Tower ---
        $tower = DB::table('activity_tower as at')
            ->leftJoin('LIST_TOWER as lt', 'at.UUID_TOWER', 'lt.UUID')
            ->leftJoin('users as us', 'at.REPORTING', 'us.nrp')
            ->select(
                'at.UUID',
                'at.STATUSENABLED',
                'at.START',
                'at.FINISH',
                DB::raw("CONCAT('(', lt.NAMA, ') ', at.ACTION_PROBLEM, ' (', at.ACTUAL_PROBLEM, ')') as ACTIVITY"),
                'at.ACTION_BY as PIC',
                'us.name as REPORTING',
                'at.DATE_ACTION as DATE_REPORT',
                DB::raw("'Tower' as TEAM")
            )
            ->where('at.STATUSENABLED', true)
            // ->where('at.DATE_ACTION', $date)
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
                DB::raw("CONCAT('(', lu.VHC_ID, ') ', un.ACTION_PROBLEM, ' (', un.ACTUAL_PROBLEM, ')') as ACTIVITY"),
                'un.ACTION_BY as PIC',
                'us.name as REPORTING',
                'un.DATE_ACTION as DATE_REPORT',
                DB::raw("'Unit' as TEAM")
            )
            ->where('un.STATUSENABLED', true)
            // ->where('un.DATE_ACTION', $date)
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
                DB::raw("CONCAT(gen.KEGIATAN, ' ', twr.NAMA, ' (', twr.NO_GENSET, '), Fuel: ', gen.FUEL, '%') as ACTIVITY"),
                'gen.ACTION_BY as PIC',
                'us.name as REPORTING',
                'gen.DATE_REPORT',
                DB::raw("'Tower' as TEAM")
            )
            ->where('gen.STATUSENABLED', true)
            // ->where('gen.DATE_REPORT', $date)
            ->get()
            ->map(function ($act) use ($convertPIC) {
                $act->PIC = $convertPIC($act->PIC);
                return $act;
            });

        $teamOrder = ['All Team', 'Tower', 'Unit'];

        $dailyActivity = collect()
            ->merge($additional)
            ->merge($tower)
            ->merge($unit)
            ->merge($genset)
            ->sortByDesc('DATE_REPORT')
            ->values();
        if (!empty($search)) {
            $dailyActivity = $dailyActivity->filter(function ($item) use ($search) {
                return stripos($item->ACTIVITY, $search) !== false ||
                    stripos($item->PIC, $search) !== false ||
                    stripos($item->REPORTING, $search) !== false ||
                    stripos($item->TEAM, $search) !== false;
            })->values();
        }

        $filteredRecords = $dailyActivity->count();

        $support = $dailyActivity->skip($start)->take($length)->values();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $filteredRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $support
        ]);

    }
}
