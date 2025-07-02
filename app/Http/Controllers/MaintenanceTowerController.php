<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceTowerController extends Controller
{
    //
    public function index()
    {
        $bulanTahun = request('DATE_REPORT') ?? Carbon::now()->format('Y-m');
            $tanggalSekarang = request('DAY') ?? now()->day;

            $carbonBulan = Carbon::createFromFormat('Y-m', $bulanTahun)->startOfMonth();

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

            $latestActivity = DB::table(DB::raw("
                (
                    SELECT
                        au.UUID_TOWER,
                        au.UUID,
                        au.DATE_ACTION,
                        au.REMARKS,
                        au.REPORTING,
                        lu.TYPE,
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

            $activityTower = DB::table('LIST_TOWER as lu')
                ->leftJoinSub($latestActivity, 'latest', function ($join) {
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
                    'latest.DATE_ACTION as LAST_MAINTAINED',
                    'lu.LOKASI as LOCATION',
                    'latest.REMARKS',
                    'us.name as REPORTING'
                )
                ->where('lu.STATUSENABLED', true)
                ->orderBy('lu.NAMA')
                ->get();

        return view('maintenanceTower.index', compact('activityTower'));
    }
}
