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
        // Ambil bulan & tahun dari request atau default ke bulan ini
        $bulanTahun = request('DATE_REPORT') ?? Carbon::now()->format('Y-m');
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


        // Subquery: Ambil aktivitas terakhir per unit untuk bulan dan act.ID = 2
        $latestActivity = DB::table(DB::raw("
            (
                SELECT
                    au.UUID_UNIT,
                    au.UUID,
                    au.DATE_ACTION,
                    au.UUID_AREA,
                    au.REMARKS,
                    au.REPORTING,
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
        ->addBinding([$bulanTahun])
        ->where('rn', 1);

        // Main query: Ambil seluruh unit, dan gabungkan dengan aktivitas maintenance bulan tersebut (jika ada)
        $activityUnit = DB::table('LIST_UNIT as lu')
            ->leftJoinSub($latestActivity, 'latest', function ($join) {
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
                'latest.DATE_ACTION as LAST_MAINTAINED',
                'la.KETERANGAN as LOCATION',
                'latest.REMARKS',
                'latest.ACTION_BY',
                'us.name as REPORTING'
            )
            ->where('lu.STATUSENABLED', true)
            ->orderBy('lu.VHC_ID')
            ->get()->map(function ($row) use ($convertPIC) {
                $row->ACTION_BY = $row->ACTION_BY ? $convertPIC($row->ACTION_BY) : null;
                return $row;
            });

        return view('maintenanceUnit.index', compact('activityUnit'));
    }
}
