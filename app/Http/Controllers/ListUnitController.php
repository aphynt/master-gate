<?php

namespace App\Http\Controllers;

use App\Exports\ListUnitExport;
use App\Models\ListUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Maatwebsite\Excel\Facades\Excel;

class ListUnitController extends Controller
{
    //
    public function index(Request $request)
    {
        $uuidConverter = DB::table('LOG_BARANG')
        ->where('ID', 99)
        ->value('UUID');

        $riwayatConverter = DB::table('log_barang_keluar as bk')
        ->join('ACTIVITY_UNIT as au', 'bk.UUID_ACTIVITY_UNIT', '=', 'au.UUID')
        ->where('bk.UUID_BARANG', $uuidConverter)
        ->where('bk.STATUSENABLED', true)
        ->whereNotNull('au.UUID_UNIT')
        ->select(
            'au.UUID_UNIT',
            DB::raw('MAX(bk.TANGGAL_KELUAR) as TANGGAL_PEMASANGAN'),
            DB::raw('COUNT(bk.UUID) as JUMLAH_RIWAYAT')
        )
        ->groupBy('au.UUID_UNIT');

        $listUnit = DB::table('LIST_UNIT as lu')
            ->leftJoinSub($riwayatConverter, 'converter', function ($join) {
                $join->on('lu.UUID', '=', 'converter.UUID_UNIT');
            })
            ->where('lu.STATUSENABLED', true)
            ->select(
                'lu.*',
                'converter.TANGGAL_PEMASANGAN',
                'converter.JUMLAH_RIWAYAT'
            )
            ->selectRaw(
                'CASE WHEN converter.UUID_UNIT IS NOT NULL THEN 1 ELSE 0 END AS STATUS_CONVERTER_OTOMATIS'
            )
            ->get();

        $totalUnit = $listUnit->count();

        $totalTerpasang = $listUnit->where('STATUS_CONVERTER_OTOMATIS', 1)->count();

        $totalBelumTerpasang = $totalUnit - $totalTerpasang;

        $totalAdaRiwayat = $listUnit->whereNotNull('TANGGAL_PEMASANGAN')->count();

        $totalAktif = $totalUnit;

        $totalNonaktif = 0;

        $persentaseTerpasang = $totalUnit > 0
            ? round(($totalTerpasang / $totalUnit) * 100, 1)
            : 0;

        $action = $request->input('action_type');

        if ($action === 'export') {
            return Excel::download(
                new ListUnitExport($listUnit),
                'List Unit.xlsx'
            );
        }

        return view('listUnit.index', compact(
            'listUnit',
            'totalUnit',
            'totalTerpasang',
            'totalBelumTerpasang',
            'totalAdaRiwayat',
            'totalAktif',
            'totalNonaktif',
            'persentaseTerpasang'
        ));
    }

    public function insert(Request $request)
    {

        try {
            ListUnit::create([
                'UUID' => (string) Uuid::uuid4()->toString(),
                'STATUSENABLED' => (int) $request->STATUSENABLED,
                'KETERANGAN' => $request->KETERANGAN,
                'ADD_BY' => Auth::user()->nrp,
                // 'CREATED_AT' => now(),
                // 'UPDATED_AT' => now(),
            ]);

            return redirect()->back()->with('success', 'Unit berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->back()->with('info', 'Terjadi kesalahan: '. $th->getMessage());
        }
    }

    public function update(Request $request, $uuid)
    {

        try {

            ListUnit::where('UUID', $uuid)->update([
                'CONVERTER_DC_TO_DC' => (int) $request->CONVERTER_DC_TO_DC,
                'STATUSENABLED' => (int) $request->STATUSENABLED,
                'UPDATED_BY' => Auth::user()->nrp,
                // 'UPDATED_AT' => now(),
            ]);

            return redirect()->back()->with('success', 'Unit berhasil diupdate');
        } catch (\Throwable $th) {
            return redirect()->back()->with('info', 'Terjadi kesalahan: '. $th->getMessage());
        }
    }
}
