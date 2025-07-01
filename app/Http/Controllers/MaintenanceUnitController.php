<?php

namespace App\Http\Controllers;

use App\Models\ActivityUnit;
use App\Models\ListUnit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceUnitController extends Controller
{
    //
    public function index()
    {
        $bulanTahun = $request->DATE_REPORT ?? Carbon::now()->format('Y-m');

        // Ambil aktivitas maintenance terbaru per unit
        $latestActivity = DB::table(DB::raw("
            (
                SELECT
                    au.UUID_UNIT,
                    au.UUID,
                    au.DATE_ACTION,
                    au.UUID_AREA,
                    au.REMARKS,
                    au.REPORTING,
                    ROW_NUMBER() OVER (PARTITION BY au.UUID_UNIT ORDER BY au.DATE_ACTION DESC, au.UUID DESC) as rn
                FROM ACTIVITY_UNIT au
                JOIN LIST_ACTIVITY act ON au.UUID_ACTIVITY = act.UUID
                WHERE au.STATUSENABLED = 1
                AND act.ID = 2
                AND FORMAT(au.DATE_ACTION, 'yyyy-MM') = ?
            ) as ranked
        "))
        ->addBinding([$bulanTahun]) // untuk parameter FORMAT()
        ->where('rn', 1);

        // Gabungkan dengan list unit
        $activityUnit = DB::table('LIST_UNIT as lu')
            ->leftJoinSub($latestActivity, 'latest', function ($join) {
                $join->on('lu.UUID', '=', 'latest.UUID_UNIT');
            })
            ->leftJoin('LIST_AREA as la', 'latest.UUID_AREA', '=', 'la.UUID')
            ->leftJoin('users as us', 'latest.REPORTING', '=', 'us.nrp')
            ->select(
                'lu.VHC_ID as NAME',
                'lu.GROUP_ID as CODE',
                DB::raw("CASE WHEN latest.UUID IS NOT NULL THEN 'Already Maintained' ELSE 'Ready For Maintenance' END as STATUS"),
                'latest.DATE_ACTION as LAST_MAINTAINED',
                'la.KETERANGAN as LOCATION',
                'latest.REMARKS',
                'us.name as REPORTING'
            )
            ->where('lu.STATUSENABLED', true)
            ->orderBy('lu.VHC_ID')
            ->get();

        return view('maintenanceUnit.index', compact('activityUnit'));
    }
}
