<?php

namespace App\Http\Controllers;

use App\Models\ActivityAdditional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DailyActivityController extends Controller
{
    //
    public function index(Request $request)
    {
        if(!empty($request->DATE_REPORT)){
            $date = $request->DATE_REPORT;
        }else{
            $date = Carbon::today()->format('Y-m-d');
        }

        $users = DB::table('users')->pluck('name', 'nrp');

        // Fungsi bantu untuk konversi PIC
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

        // --- Ambil data Additional ---
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
            ->where('add.DATE_REPORT', $date)
            ->get()
            ->map(function ($act) use ($convertPIC) {
                $act->PIC = $convertPIC($act->PIC);
                return $act;
            });

        // --- Ambil data Tower ---
        $tower = DB::table('activity_tower as at')
            ->leftJoin('users as us', 'at.REPORTING', 'us.nrp')
            ->select(
                'at.UUID',
                'at.STATUSENABLED',
                'at.START',
                'at.FINISH',
                DB::raw("CONCAT(at.ACTION_PROBLEM, ' (', at.ACTUAL_PROBLEM, ')') as ACTIVITY"),
                'at.ACTION_BY as PIC',
                'us.name as REPORTING',
                'at.DATE_ACTION as DATE_REPORT',
                DB::raw("'Network' as TEAM")
            )
            ->where('at.STATUSENABLED', true)
            ->where('at.DATE_ACTION', $date)
            ->get()
            ->map(function ($act) use ($convertPIC) {
                $act->PIC = $convertPIC($act->PIC);
                return $act;
            });

        // --- Ambil data Unit ---
        $unit = DB::table('ACTIVITY_UNIT as un')
            ->leftJoin('users as us', 'un.REPORTING', 'us.nrp')
            ->select(
                'un.UUID',
                'un.STATUSENABLED',
                'un.START',
                'un.FINISH',
                DB::raw("CONCAT(un.ACTION_PROBLEM, ' (', un.ACTUAL_PROBLEM, ')') as ACTIVITY"),
                'un.ACTION_BY as PIC',
                'us.name as REPORTING',
                'un.DATE_ACTION as DATE_REPORT',
                DB::raw("'Hardware' as TEAM")
            )
            ->where('un.STATUSENABLED', true)
            ->where('un.DATE_ACTION', $date)
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
                DB::raw("'-' as PIC"),
                'us.name as REPORTING',
                'gen.DATE_REPORT',
                DB::raw("'Network' as TEAM")
            )
            ->where('gen.STATUSENABLED', true)
            ->where('gen.DATE_REPORT', $date)
            ->get();

        // --- Gabungkan semua ---
        $dailyActivity = collect()
            ->merge($additional)
            ->merge($tower)
            ->merge($unit)
            ->merge($genset)
            ->sortBy('START')
            ->values();

        $data = [
            'dailyActivity' => $dailyActivity,
        ];

        return view('dailyActivity.index', compact('data'));
    }
}
