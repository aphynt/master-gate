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
        $targetDate = Carbon::now();
        $targetMonth = $targetDate->month;
        $targetYear = $targetDate->year;

        if(!empty($request->DATE_REPORT)){
            $bulanTahun = request('DATE_REPORT') ?? Carbon::now()->format('Y-m');
        }else{
            $date = Carbon::today()->format('Y-m');
        }

        $bulanTahun = request('DATE_REPORT') ?? Carbon::now()->format('Y-m');


        $latestActivity = DB::table('ACTIVITY_UNIT as au')
            ->select('au.UUID_UNIT', DB::raw("MAX(au.DATE_ACTION) as LAST_MAINTENANCE"))
            ->where('au.STATUSENABLED', true)
            ->whereRaw("FORMAT(DATE_ACTION, 'yyyy-MM') = ?", [$bulanTahun])
            ->groupBy('au.UUID_UNIT');


        $activityUnit = DB::table('LIST_UNIT as lu')
            ->leftJoinSub($latestActivity, 'latest', function ($join) {
                $join->on('lu.UUID', '=', 'latest.UUID_UNIT');
            })
            ->leftJoin('ACTIVITY_UNIT as au', function ($join) {
                $join->on('lu.UUID', '=', 'au.UUID_UNIT')
                    ->on('au.DATE_ACTION', '=', 'latest.LAST_MAINTENANCE');
            })
            ->leftJoin('LIST_AREA as la', 'au.UUID_AREA', '=', 'la.UUID')
            ->leftJoin('users as us', 'au.REPORTING', '=', 'us.nrp')
            ->select(
                'lu.VHC_ID as NAME',
                'lu.GROUP_ID as CODE',
                DB::raw("CASE WHEN au.UUID IS NOT NULL THEN 'Already Maintained' ELSE 'Ready For Maintenance' END as STATUS"),
                'au.DATE_ACTION as LAST_MAINTAINED',
                'la.KETERANGAN as LOCATION',
                'au.REMARKS',
                'us.name as REPORTING'
            )
            ->where('lu.STATUSENABLED', true)
            ->orderBy('lu.VHC_ID')
            ->get();

        return view('maintenanceUnit.index', compact('activityUnit'));
    }
}
