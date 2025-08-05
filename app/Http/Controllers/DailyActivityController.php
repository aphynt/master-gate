<?php

namespace App\Http\Controllers;

use App\Models\ActivityAdditional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SummaryDailyExport;

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
            ->where('add.DATE_REPORT', $date)
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
            ->where('at.DATE_ACTION', $date)
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
                'gen.ACTION_BY as PIC',
                'us.name as REPORTING',
                'gen.DATE_REPORT',
                DB::raw("'Tower' as TEAM")
            )
            ->where('gen.STATUSENABLED', true)
            ->where('gen.DATE_REPORT', $date)
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
            ->sortBy(function ($item) use ($teamOrder) {
                $teamIndex = array_search($item->TEAM, $teamOrder);
                $teamIndex = $teamIndex === false ? PHP_INT_MAX : $teamIndex;
                return sprintf('%03d-%s', $teamIndex, $item->START);
            })
            ->values();

        $bulanTahun = request()->filled('DATE_REPORT') ? substr(request('DATE_REPORT'), 0, 7) : Carbon::now()->format('Y-m');

        $repairTower = DB::table('activity_tower as at')
        ->leftJoin('list_tower as lt', 'at.UUID_TOWER', 'lt.UUID')
        ->leftJoin('list_activity as la', 'at.UUID_ACTIVITY', 'la.UUID')
        ->leftJoin('list_status as ls', 'at.UUID_STATUS', 'ls.UUID')
        ->leftJoin('users as us', 'at.REPORTING', 'us.nrp')
        ->select(
            'at.UUID',
            'LT.TYPE_DESC',
            'LT.LOKASI',
            'lt.NAMA as NAMA_TOWER',
            DB::raw("FORMAT(at.DATE_ACTION, 'yyyy-MM-dd') as DATE_ACTION"),
            'la.KETERANGAN as NAMA_ACTIVITY',
            'at.ACTUAL_PROBLEM',
            'at.ACTION_PROBLEM',
            'at.START',
            'at.FINISH',
            'ls.KETERANGAN as NAMA_STATUS',
            'at.ACTION_BY',
            'at.REMARKS',
            'us.name as REPORTING',
            'at.REPORTING as NRP_REPORTING',
        )
        ->where('at.STATUSENABLED', true)
        ->where('la.ID', 1)
        ->whereRaw("FORMAT(at.DATE_ACTION, 'yyyy-MM') = ?", [$bulanTahun])
        ->get();

        foreach ($repairTower as $act) {
            $nrps = explode(',', $act->ACTION_BY);
            $names = [];

            foreach ($nrps as $nrp) {
                $nrp = trim($nrp);
                if (isset($users[$nrp])) {
                    $names[] = $users[$nrp];
                }
            }

            $act->ACTION_BY = implode(', ', $names);
        }


        $repairUnit = DB::table('activity_unit as un')
        ->leftJoin('list_unit as lt', 'un.UUID_UNIT', 'lt.UUID')
        ->leftJoin('list_activity as la', 'un.UUID_ACTIVITY', 'la.UUID')
        ->leftJoin('list_status as ls', 'un.UUID_STATUS', 'ls.UUID')
        ->leftJoin('list_area as ar', 'un.UUID_AREA', 'ar.UUID')
        ->leftJoin('users as us', 'un.REPORTING', 'us.nrp')
        ->select(
            'un.UUID',
            'lt.VHC_ID as NAMA_UNIT',
            'lt.TYPE_ID as TYPE_DESC',
            DB::raw("FORMAT(un.DATE_ACTION, 'yyyy-MM-dd') as DATE_ACTION"),
            'la.KETERANGAN as NAMA_ACTIVITY',
            'un.ACTUAL_PROBLEM',
            'un.ACTION_PROBLEM',
            'un.START',
            'un.FINISH',
            'ar.KETERANGAN as LOKASI',
            'ls.KETERANGAN as NAMA_STATUS',
            'un.ACTION_BY',
            'un.REMARKS',
            'us.name as REPORTING',
            'un.REPORTING as NRP_REPORTING',
        )
        ->where('un.STATUSENABLED', true)
        ->where('la.ID', 1)
        ->whereRaw("FORMAT(un.DATE_ACTION, 'yyyy-MM') = ?", [$bulanTahun])
        ->get();

        foreach ($repairUnit as $act) {
            $nrps = explode(',', $act->ACTION_BY);
            $names = [];

            foreach ($nrps as $nrp) {
                $nrp = trim($nrp);
                if (isset($users[$nrp])) {
                    $names[] = $users[$nrp];
                }
            }

            $act->ACTION_BY = implode(', ', $names);
        }

        $bulanUnit = request('DATE_REPORT')
            ? Carbon::parse(request('DATE_REPORT'))->format('Y-m')
            : Carbon::now()->format('Y-m');

            $tanggalSekarang = request('DAY') ?? now()->day;

            $carbonBulan = Carbon::parse($bulanTahun)->startOfMonth();

            // Tentukan periode MT berdasarkan tanggal hari ini
            if ($tanggalSekarang < 16) {
                // Periode 1
                $mtStart = $carbonBulan->copy()->startOfMonth()->toDateString();
                $mtEnd = $carbonBulan->copy()->day(15)->toDateString();
            } else {
                // Periode 2
                $mtStart = $carbonBulan->copy()->day(16)->toDateString();
                $mtEnd = $carbonBulan->copy()->endOfMonth()->toDateString();
            }

            // Untuk unit selain MT → tetap dari awal sampai akhir bulan
            $nonMtStart = $carbonBulan->copy()->startOfMonth()->toDateString();
            $nonMtEnd = $carbonBulan->copy()->endOfMonth()->toDateString();


        $latestActivityTower = DB::table(DB::raw("
            (
                SELECT
                    au.UUID_TOWER as UUID_TOWER,
                    au.UUID as UUID,
                    au.DATE_ACTION as DATE_ACTION,
                    au.REMARKS as REMARKS,
                    au.REPORTING as REPORTING,
                    lu.TYPE as TYPE,
                    au.START as START,
                    au.ACTION_BY as ACTION_BY,
                    ROW_NUMBER() OVER (
                        PARTITION BY au.UUID_TOWER
                        ORDER BY au.DATE_ACTION DESC, au.UUID DESC
                    ) as rn
                FROM ACTIVITY_TOWER au
                JOIN LIST_ACTIVITY act ON au.UUID_ACTIVITY = act.UUID
                JOIN LIST_TOWER lu ON au.UUID_TOWER = lu.UUID
                WHERE
                    au.STATUSENABLED = 1
                    AND act.ID = 2
                    AND (
                        (lu.TYPE = 'MT' AND au.DATE_ACTION BETWEEN ? AND ?)
                        OR
                        (lu.TYPE != 'MT' AND au.DATE_ACTION BETWEEN ? AND ?)
                    )
            ) as ranked
        "))
        ->addBinding([$mtStart, $mtEnd, $nonMtStart, $nonMtEnd])
        ->where('rn', 1);

        $maintenanceTower = DB::table('LIST_TOWER as lu')
            ->leftJoinSub($latestActivityTower, 'latest', function ($join) {
                $join->on('lu.UUID', '=', 'latest.UUID_TOWER');
            })
            ->leftJoin('users as us', 'latest.REPORTING', '=', 'us.nrp')
            ->select(
                'lu.NAMA as NAME',
                'lu.TYPE as CODE',
                DB::raw("CASE
                    WHEN latest.UUID IS NOT NULL THEN 'Already Maintained'
                    ELSE 'Ready For Maintenance'
                END as STATUS"),
                DB::raw("FORMAT(CAST(latest.DATE_ACTION AS DATETIME) + CAST(latest.START AS DATETIME), 'yyyy-MM-dd HH:mm') as LAST_MAINTAINED"),
                'lu.LOKASI as LOCATION',
                'latest.REMARKS',
                'latest.ACTION_BY',
                'us.name as REPORTING'
            )
            ->where('lu.STATUSENABLED', true)
            ->orderBy('lu.NAMA')
            ->get()
            ->map(function ($row) use ($convertPIC) {
                $row->ACTION_BY = $row->ACTION_BY ? $convertPIC($row->ACTION_BY) : null;
                return $row;
            });

        $latestActivityUnit = DB::table(DB::raw("
            (
                SELECT
                    au.UUID_UNIT,
                    au.UUID,
                    au.DATE_ACTION,
                    au.UUID_AREA,
                    au.REMARKS,
                    au.REPORTING,
                    au.START,
                    au.ACTION_BY as ACTION_BY,
                    ROW_NUMBER() OVER (
                        PARTITION BY au.UUID_UNIT
                        ORDER BY au.DATE_ACTION DESC, au.UUID DESC
                    ) as rn
                FROM ACTIVITY_UNIT au
                JOIN LIST_ACTIVITY act ON au.UUID_ACTIVITY = act.UUID
                WHERE
                    au.STATUSENABLED = 1
                    AND act.ID = 2
                    AND FORMAT(au.DATE_ACTION, 'yyyy-MM') = ?
            ) as ranked
        "))
        ->addBinding([$bulanUnit])
        ->where('rn', 1);

        $maintenanceUnit = DB::table('LIST_UNIT as lu')
            ->leftJoinSub($latestActivityUnit, 'latest', function ($join) {
                $join->on('lu.UUID', '=', 'latest.UUID_UNIT');
            })
            ->leftJoin('LIST_AREA as la', 'latest.UUID_AREA', '=', 'la.UUID')
            ->leftJoin('users as us', 'latest.REPORTING', '=', 'us.nrp')
            ->select(
                'lu.VHC_ID as NAME',
                'lu.GROUP_ID as CODE',
                DB::raw("CASE
                    WHEN latest.UUID IS NOT NULL THEN 'Already Maintained'
                    ELSE 'Ready For Maintenance'
                END as STATUS"),
                DB::raw("FORMAT(CAST(latest.DATE_ACTION AS DATETIME) + CAST(latest.START AS DATETIME), 'yyyy-MM-dd HH:mm') as LAST_MAINTAINED"),
                'la.KETERANGAN as LOCATION',
                'latest.REMARKS',
                'latest.ACTION_BY',
                'us.name as REPORTING'
            )
            ->where('lu.STATUSENABLED', true)
            ->orderBy('lu.VHC_ID')
            ->get()
            ->map(function ($row) use ($convertPIC) {
                $row->ACTION_BY = $row->ACTION_BY ? $convertPIC($row->ACTION_BY) : null;
                return $row;
            });

        if ($action === 'export') {
            return Excel::download(new SummaryDailyExport($dailyActivity, $repairTower, $repairUnit, $maintenanceTower, $maintenanceUnit, $date), 'Summary Activity ' . Carbon::parse($request->DATE_REPORT)->translatedFormat('d F Y') . '.xlsx');
        }

        return view('dailyActivity.index', [
            'data' => [
                'dailyActivity' => $dailyActivity,
            ]
        ]);
    }
}
