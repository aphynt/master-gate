<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $today = Carbon::today();
        $yesterday = $today->copy()->subDay()->format('Y-m-d');
        $today = $today->format('Y-m-d');
        $bulanUnit = request('DATE_REPORT')
            ? Carbon::parse(request('DATE_REPORT'))->format('Y-m')
            : Carbon::now()->format('Y-m');

        $tanggalSekarang = request('DAY') ?? now()->day;
        $bulanTahun = request()->filled('DATE_REPORT') ? substr(request('DATE_REPORT'), 0, 7) : Carbon::now()->format('Y-m');
        $carbonBulan = Carbon::parse($bulanTahun)->startOfMonth();


        // periode MT berdasarkan tanggal hari ini
        if ($tanggalSekarang < 16) {
            // Periode 1
            $mtStart = $carbonBulan->copy()->startOfMonth()->toDateString();
            $mtEnd = $carbonBulan->copy()->day(15)->toDateString();
        } else {
            // Periode 2
            $mtStart = $carbonBulan->copy()->day(16)->toDateString();
            $mtEnd = $carbonBulan->copy()->endOfMonth()->toDateString();
        }

        $nonMtStart = $carbonBulan->copy()->startOfMonth()->toDateString();
        $nonMtEnd = $carbonBulan->copy()->endOfMonth()->toDateString();

        // Ambil mapping NRP ke nama
        $userMap = DB::table('users')->pluck('name', 'nrp');

        // Fungsi konversi NRP menjadi nama
        $convertPIC = fn($picString) => collect(explode(',', $picString))
            ->map(fn($nrp) => trim($nrp))
            ->filter(fn($nrp) => isset($userMap[$nrp]))
            ->map(fn($nrp) => $userMap[$nrp])
            ->implode(', ');

        // Subquery data terakhir maintenance per unit
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
                    au.ACTION_BY,
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
        "))->addBinding([$bulanUnit])
        ->where('rn', 1);

        // Gabungkan dengan data unit dan lokasi
        $maintenanceUnit = DB::table('LIST_UNIT as lu')
            ->leftJoinSub($latestActivityUnit, 'latest', fn($join) =>
                $join->on('lu.UUID', '=', 'latest.UUID_UNIT'))
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

        // Hitung data summary
        $dataUnit = collect($maintenanceUnit);

        $totalPlanAllUnit = $dataUnit->count();
        $monthlyMaintainedUnit = $dataUnit->where('STATUS', 'Already Maintained')->count();
        $readyForMaintenance = $dataUnit->where('STATUS', 'Ready For Maintenance')->count();
        $todayMaintainedUnit = $dataUnit->filter(fn($item) =>
            $item->STATUS === 'Already Maintained' &&
            Carbon::parse($item->LAST_MAINTAINED)->format('Y-m-d') === $today
        )->count();

        $yesterdayMaintainedUnit = $dataUnit->filter(fn($item) =>
            $item->STATUS === 'Already Maintained' &&
            Carbon::parse($item->LAST_MAINTAINED)->format('Y-m-d') === $yesterday
        )->count();

        $dataTower = collect($maintenanceTower);
        $totalPlanAllTower = $dataTower->count();
        $monthlyMaintainedTower = $dataTower->where('STATUS', 'Already Maintained')->count();
        $readyForMaintenance = $dataTower->where('STATUS', 'Ready For Maintenance')->count();
        $todayMaintainedTower = $dataTower->filter(fn($item) =>
            $item->STATUS === 'Already Maintained' &&
            Carbon::parse($item->LAST_MAINTAINED)->format('Y-m-d') === $today
        )->count();

        $yesterdayMaintainedTower = $dataTower->filter(fn($item) =>
            $item->STATUS === 'Already Maintained' &&
            Carbon::parse($item->LAST_MAINTAINED)->format('Y-m-d') === $yesterday
        )->count();


        // Persentase
        $percentage = fn($value, $total) => $total > 0 ? round(($value / $total) * 100) . '' : '0';

        $todayMaintainedPercentUnit = $percentage($todayMaintainedUnit, 7);
        $yesterdayMaintainedPercentUnit = $percentage($yesterdayMaintainedUnit, 7);
        $monthlyMaintainedPercentUnit = $percentage($monthlyMaintainedUnit, $totalPlanAllUnit);

        $todayMaintainedPercentTower = $percentage($todayMaintainedTower, 1);
        $yesterdayMaintainedPercentTower = $percentage($yesterdayMaintainedTower, 1);
        $monthlyMaintainedPercentTower = $percentage($monthlyMaintainedTower, $totalPlanAllTower);

        $statusUnit = collect(DB::connection('focus')->select('SET NOCOUNT ON; EXEC FOCUS_REPORTING.DBO.RPT_DASHBOARD_RESUME_TOTAL_UNIT'));

        $totalBarang = Barang::where('STATUSENABLED', true)->count();

        $totalBarangBulanan = Barang::where('STATUSENABLED', true)->count();

        $totalBarangMasukBulanan = BarangMasuk::where('STATUSENABLED', true)->whereBetween('TANGGAL_MASUK', [$nonMtStart, $nonMtEnd])->count();
        $totalBarangKeluarBulanan = BarangKeluar::where('STATUSENABLED', true)->whereBetween('TANGGAL_KELUAR', [$nonMtStart, $nonMtEnd])->count();

        $totalBarangKeluarHarian = BarangKeluar::where('STATUSENABLED', true)->where('TANGGAL_KELUAR', $today)->count();


        $dataSummary = [
            'todayMaintainedUnit' => $todayMaintainedUnit,
            'todayMaintainedPercentUnit' => $todayMaintainedPercentUnit,

            'yesterdayMaintainedUnit' => $yesterdayMaintainedUnit,
            'yesterdayMaintainedPercentUnit' => $yesterdayMaintainedPercentUnit,

            'monthlyMaintainedUnit' => $monthlyMaintainedUnit,
            'monthlyMaintainedPercentUnit' => $monthlyMaintainedPercentUnit,

            'todayMaintainedTower' => $todayMaintainedTower,
            'todayMaintainedPercentTower' => $todayMaintainedPercentTower,

            'yesterdayMaintainedTower' => $yesterdayMaintainedTower,
            'yesterdayMaintainedPercentTower' => $yesterdayMaintainedPercentTower,

            'monthlyMaintainedTower' => $monthlyMaintainedTower,
            'monthlyMaintainedPercentTower' => $monthlyMaintainedPercentTower,

            'statusUnit' => $statusUnit,
        ];

        return view('dashboard.index', compact('dataSummary'));
    }
}
