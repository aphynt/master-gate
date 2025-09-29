<?php

namespace App\Http\Controllers;

use App\Exports\SummaryWeeklyExport;
use App\Models\WeeklyPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class WeeklyActivityController extends Controller
{
    //
    public function index(Request $request)
    {
        if(!empty($request->DATE_REPORT)){
            $tanggal = $request->DATE_REPORT;
        }else{
            $tanggal = Carbon::today()->format('Y-m-d');
        }

        $tanggalSekarang = Carbon::parse($tanggal);
        $selisihHari = ($tanggalSekarang->dayOfWeek - 3 + 7) % 7;
        $startDate = $tanggalSekarang->copy()->subDays($selisihHari);
        $endDate = $startDate->copy()->addDays(6);

        $startDateMingguDepan = $startDate->copy()->addDays(7);
        $endDateMingguDepan = $startDateMingguDepan->copy()->addDays(6);


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

        $weekly = DB::table('PLAN_WEEKLY as pw')
        ->leftJoin('LIST_TEAM as lt', 'pw.TEAM', 'lt.UUID')
        ->leftJoin('users as us', 'pw.REPORTING', 'us.nrp')
        ->select(
            'pw.UUID',
            'pw.STARTDATE',
            'pw.ENDDATE',
            'pw.WORK_ITEMS',
            'pw.ACTION_BY',
            'lt.NAMA as TEAM',
            'us.nrp as REPORTING',
        )
        ->where('pw.STATUSENABLED', true)
            ->whereDate('pw.STARTDATE', '>=', $startDateMingguDepan)
            ->whereDate('pw.ENDDATE', '<=', $endDateMingguDepan)
            ->get()
            ->map(function ($row) use ($convertPIC) {
                $row->ACTION_BY = $row->ACTION_BY ? $convertPIC($row->ACTION_BY) : null;
                return $row;
            });

        $additional = DB::table('activity_additional as add')
            ->leftJoin('list_team as team', 'add.UUID_TEAM', 'team.UUID')
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
                'team.NAMA as TEAM',
            )
            ->where('add.STATUSENABLED', true)
            ->whereBetween('add.DATE_REPORT', [$startDate, $endDate])
            ->get()
            ->map(function ($act) use ($convertPIC) {
                $act->PIC = $convertPIC($act->PIC);
                return $act;
            });


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
            ->whereBetween('at.DATE_ACTION', [$startDate, $endDate])
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
            ->whereBetween('un.DATE_ACTION', [$startDate, $endDate])
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
            ->whereBetween('gen.DATE_REPORT', [$startDate, $endDate])
            ->get()
            ->map(function ($act) use ($convertPIC) {
                $act->PIC = $convertPIC($act->PIC);
                return $act;
            });

        $teamOrder = ['Tower', 'Unit', 'All Team'];

        $monthlyActivity = collect()
            ->merge($tower)
            ->merge($unit)
            ->merge($genset)
            ->merge($additional)
            ->sortBy(function ($item) use ($teamOrder) {
                $teamIndex = array_search($item->TEAM, $teamOrder);
                $teamIndex = $teamIndex === false ? PHP_INT_MAX : $teamIndex;
                return sprintf('%03d-%s', $teamIndex, $item->DATE_REPORT);
            })
            ->values();

            $data = [
                'monthlyActivity' => $monthlyActivity,
                'weeklyActivity' => $weekly,
            ];


            if ($action === 'export') {
            return Excel::download(new SummaryWeeklyExport($monthlyActivity, $weekly, $tanggal, $startDate, $endDateMingguDepan), 'Weekly Activity dan Plan ' . Carbon::parse($startDate)->translatedFormat('d F Y') . ' - ' . Carbon::parse($endDateMingguDepan)->translatedFormat('d F Y') .'.xlsx');
        }
        return view('weeklyActivity.index', compact('data'));
    }
}
