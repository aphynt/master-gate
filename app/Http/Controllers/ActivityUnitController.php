<?php

namespace App\Http\Controllers;

use App\Models\ActivityUnit;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\ListActivity;
use App\Models\ListArea;
use App\Models\ListDescriptionProblem;
use App\Models\ListRequestAt;
use App\Models\ListStatus;
use App\Models\ListUnit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class ActivityUnitController extends Controller
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

        $unit = DB::table('activity_unit as un')
        ->leftJoin('list_unit as lt', 'un.UUID_UNIT', 'lt.UUID')
        ->leftJoin('list_activity as la', 'un.UUID_ACTIVITY', 'la.UUID')
        ->leftJoin('list_status as ls', 'un.UUID_STATUS', 'ls.UUID')
        ->leftJoin('users as us', 'un.REPORTING', 'us.nrp')
        ->select(
            'un.UUID',
            'lt.VHC_ID as NAMA_UNIT',
            DB::raw("FORMAT(un.DATE_ACTION, 'yyyy-MM-dd') as DATE_ACTION"),
            'la.KETERANGAN as NAMA_ACTIVITY',
            'un.ACTUAL_PROBLEM',
            'un.ACTION_PROBLEM',
            'un.START',
            'un.FINISH',
            'ls.KETERANGAN as NAMA_STATUS',
            'un.ACTION_BY',
            'un.REMARKS',
            'us.name as REPORTING',
            'un.REPORTING as NRP_REPORTING',
        )
        ->where('un.STATUSENABLED', true)
        ->where('un.DATE_ACTION', $date)
        ->get();
        // dd($unit);

        foreach ($unit as $act) {
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
        // dd($unit);

        return view('activityUnit.index', compact('unit'));
    }

    public function insert()
    {
        $unit = ListUnit::where('STATUSENABLED', true)->get();
        $activity = ListActivity::where('STATUSENABLED', true)->get();
        $reqBy = ListRequestAt::where('STATUSENABLED', true)->get();
        $status = ListStatus::where('STATUSENABLED', true)->get();
        $barang = Barang::where('STATUSENABLED', true)->get();
        $area = ListArea::where('STATUSENABLED', true)->get();
        $user = User::select('UUID', 'name as NAME', 'nama_panggilan as NAMA_PANGGILAN', 'NRP')->where('STATUSENABLED', true)->where('role', '!=', 'ADMIN')->get();

        return view('activityUnit.insert', compact('unit', 'user', 'activity', 'reqBy', 'status', 'barang', 'area'));
    }

    public function post(Request $request)
    {
        $activities = $request->input('data');
        $consumables = $request->filled('CONSUMABLE') ? json_decode($request->input('CONSUMABLE'), true) : [];

        try {
            // Menyimpan unit yang sudah diproses untuk consumable
            $unitConsumableDone = [];

            foreach ($activities as $act) {
                $activity = ActivityUnit::create([
                    'UUID' => $act['UUID'],
                    'STATUSENABLED' => true,
                    'UUID_UNIT' => $act['UUID_UNIT'],
                    'DATE_ACTION' => $request->DATE_REPORT,
                    'UUID_REQUEST_BY' => $act['UUID_REQUEST_BY'],
                    'UUID_ACTIVITY' => $act['UUID_ACTIVITY'],
                    'ACTUAL_PROBLEM' => $act['ACTUAL_PROBLEM'],
                    'ACTION_PROBLEM' => $act['ACTION_PROBLEM'],
                    'START' => normalizeTime($act['START']),
                    'FINISH' => normalizeTime($act['FINISH']),
                    'UUID_STATUS' => $act['STATUS'],
                    'UUID_AREA' => $act['UUID_AREA'],
                    'ACTION_BY' => is_array($act['ACTION_BY']) ? implode(',', $act['ACTION_BY']) : $act['ACTION_BY'],
                    'REMARKS' => $act['REMARKS'],
                    'REPORTING' => Auth::user()->nrp,
                    'ADD_BY' => Auth::user()->nrp,
                ]);

                // Hanya simpan consumable jika unit belum diproses
                if (!in_array($act['UUID_UNIT'], $unitConsumableDone)) {
                    $filteredConsumables = array_filter($consumables ?? [], fn($cons) => $cons['unitUUID'] == $act['UUID_UNIT']);

                    foreach ($filteredConsumables as $cons) {
                        if (!is_array($cons)) {
                            continue;
                        }
                        BarangKeluar::create([
                            'UUID' => (string) Uuid::uuid4()->toString(),
                            'STATUSENABLED' => true,
                            'UUID_BARANG' => $cons['uuid'],
                            'TANGGAL_KELUAR' => $request->DATE_REPORT,
                            'JUMLAH' => $cons['qty'],
                            'PIC' => is_array($act['ACTION_BY']) ? implode(',', $act['ACTION_BY']) : $act['ACTION_BY'],
                            'UUID_ACTIVITY_UNIT' => $activity->UUID,
                            'KETERANGAN' => $act['REMARKS'],
                            'REPORTING' => Auth::user()->nrp,
                            'ADD_BY' => Auth::user()->nrp,
                        ]);
                    }

                    // Tandai unit sudah diproses
                    $unitConsumableDone[] = $act['UUID_UNIT'];
                }
            }

            return redirect()->route('activityUnit.index')->with('success', 'Semua activity unit berhasil ditambahkan.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('info', 'Terjadi kesalahan: '. $th->getMessage());
        }


    }

    public function update(Request $request)
    {
        $request->validate([
            'data' => 'required|array',
        ]);

        $activities = $request->input('data');
        $consumables = [];

        if ($request->has('CONSUMABLE') && $request->input('CONSUMABLE') !== null) {
            $consumables = json_decode($request->input('CONSUMABLE'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()->back()->with('info', 'Format consumables tidak valid.');
            }
        }

        foreach ($activities as $idx => $row) {
            $json = $row['CONSUMABLE'] ?? '[]';
            $parsed = json_decode($json, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()->back()->with('info', 'Format consumable di row ' . $idx . ' tidak valid.');
            }

            if (is_array($parsed)) {
                $consumables = array_merge($consumables, $parsed);
            }
        }

        try {
            $consumables = is_array($consumables) ? $consumables : [];

            foreach ($activities as $row) {
                $activity = ActivityUnit::where('UUID', $row['UUID'])->first();

                if ($activity) {
                    $uuidActivity = $activity->UUID;
                    $uuidUnit = $row['UUID_UNIT'];

                    // Proses ACTION_BY jika ada
                    // $cleanedActionBy = [];
                    // if (isset($row['ACTION_BY']) && !empty($row['ACTION_BY'])) {
                    //     $actionByArray = is_array($row['ACTION_BY']) ? $row['ACTION_BY'] : explode(',', $row['ACTION_BY']);
                    //     $cleanedActionBy = array_unique(array_map('trim', $actionByArray));
                    // }

                    $updateData = [
                        'UUID_UNIT' => $uuidUnit,
                        'DATE_ACTION' => $row['DATE_REPORT'],
                        'UUID_REQUEST_BY' => $row['UUID_REQUEST_BY'],
                        'UUID_ACTIVITY' => $row['UUID_ACTIVITY'],
                        'ACTUAL_PROBLEM' => $row['ACTUAL_PROBLEM'] ?? '',
                        'ACTION_PROBLEM' => $row['ACTION_PROBLEM'] ?? '',
                        'START'          => normalizeTime($row['START']),
                        'FINISH'         => normalizeTime($row['FINISH']),
                        'UUID_STATUS' => $row['STATUS'],
                        'REMARKS' => $row['REMARKS'] ?? '',
                        'UPDATED_BY' => Auth::user()->nrp,
                    ];

                    // if (!empty($cleanedActionBy)) {
                    //     $updateData['ACTION_BY'] = implode(',', $cleanedActionBy);
                    // }

                    ActivityUnit::where('UUID', $uuidActivity)->update($updateData);

                    BarangKeluar::where('UUID_ACTIVITY_UNIT', $uuidActivity)
                        ->update(['STATUSENABLED' => false]);

                    foreach ($consumables as $group) {
                        if (!is_array($group)) {
                            continue;
                        }
                        foreach ($group as $cons) {
                            if (isset($cons['unitUUID']) && $cons['unitUUID'] == $uuidUnit) {
                                BarangKeluar::create([
                                    'UUID' => (string) Uuid::uuid4()->toString(),
                                    'STATUSENABLED' => true,
                                    'UUID_BARANG' => $cons['uuid'],
                                    'TANGGAL_KELUAR' => $row['DATE_REPORT'],
                                    'JUMLAH' => $cons['qty'],
                                    'UUID_ACTIVITY_UNIT' => $uuidActivity,
                                    'KETERANGAN' => $activity->REMARKS,
                                    'REPORTING' => Auth::user()->nrp,
                                    'ADD_BY' => Auth::user()->nrp,
                                    'PIC' => !empty($cleanedActionBy) ? implode(',', $cleanedActionBy) : null,
                                ]);
                            }
                        }
                    }
                }
            }

            return redirect()->route('activityUnit.index')->with('success', 'Semua data berhasil diperbarui!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('info', 'Terjadi kesalahan: ' . $th->getMessage());
        }


    }

    public function edit(Request $request)
    {
        $idsString = $request->query('ids');

        if (!$idsString) {
            return redirect()->route('activityUnit.index')
                ->with('error', 'Tidak ada data yang dipilih untuk diedit.');
        }

        $ids = explode(',', $idsString);

        $users = DB::table('users')->pluck('nama_panggilan', 'nrp');
        $unit = ListUnit::where('STATUSENABLED', true)->get();
        $activity = ListActivity::where('STATUSENABLED', true)->get();
        $reqBy = ListRequestAt::where('STATUSENABLED', true)->get();
        $actual = ListDescriptionProblem::where('STATUSENABLED', true)->get();
        $status = ListStatus::where('STATUSENABLED', true)->get();
        $barang = Barang::where('STATUSENABLED', true)->get();
        $user = User::select('UUID', 'name as NAME', 'nama_panggilan as NAMA_PANGGILAN', 'NRP')->where('STATUSENABLED', true)->where('role', '!=', 'ADMIN')->get();

        $dailyUnit = DB::table('activity_unit as at')
            ->leftJoin('list_unit as lt', 'at.UUID_UNIT', 'lt.UUID')
            ->leftJoin('list_request_at as ra', 'at.UUID_REQUEST_BY', 'ra.UUID')
            ->leftJoin('list_activity as la', 'at.UUID_ACTIVITY', 'la.UUID')
            ->leftJoin('list_status as ls', 'at.UUID_STATUS', 'ls.UUID')
            ->leftJoin('users as us', 'at.REPORTING', 'us.nrp')
            ->select(
                'at.UUID',
                'lt.UUID as UUID_UNIT',
                'lt.VHC_ID as NAMA_UNIT',
                DB::raw("FORMAT(at.DATE_ACTION, 'yyyy-MM-dd') as DATE_REPORT"),
                'ra.UUID as UUID_REQUEST_BY',
                'ra.KETERANGAN as NAMA_REQUEST_BY',
                'la.UUID as UUID_ACTIVITY',
                'la.KETERANGAN as NAMA_ACTIVITY',
                'at.ACTUAL_PROBLEM',
                'at.ACTION_PROBLEM',
                DB::raw("CONVERT(VARCHAR(5), at.START, 108) as START"),
                DB::raw("CONVERT(VARCHAR(5), at.FINISH, 108) as FINISH"),
                'ls.UUID as UUID_STATUS',
                'ls.KETERANGAN as NAMA_STATUS',
                'at.ACTION_BY',
                'at.REMARKS',
                'us.name as REPORTING',
                'at.REPORTING as NRP_REPORTING',
            )
            ->whereIn('at.UUID', $ids)
            ->where('at.STATUSENABLED', true)
            ->get();

        if ($dailyUnit->isEmpty()) {
            return redirect()->route('activityUnit.index')
                ->with('error', 'Data yang dipilih tidak ditemukan.');
        }

        $activityUUIDs = $dailyUnit->pluck('UUID');

        $barangKeluar = DB::table('log_barang_keluar as bk')
        ->leftJoin('log_barang as br', 'bk.UUID_BARANG', 'br.UUID')
        ->leftJoin('activity_unit as at', 'bk.UUID_ACTIVITY_UNIT', 'at.UUID')
        ->leftJoin('list_unit as unt', 'at.UUID_UNIT', 'unt.UUID')
        ->select(
            'unt.UUID as UUID_UNIT',
            'unt.VHC_ID as NAMA_UNIT',
            'br.UUID as UUID_BARANG',
            'br.ITEM as NAMA_BARANG',
            'bk.JUMLAH',
            'bk.STATUSENABLED'
        )
        ->whereIn('UUID_ACTIVITY_UNIT', $activityUUIDs)->where('bk.STATUSENABLED', true)->get();

        $barangKeluar = $barangKeluar->map(function ($item) {
        return [
            'unit' => $item->NAMA_UNIT,
            'item' => $item->NAMA_BARANG,
            'qty' => $item->JUMLAH,
            'unitUUID' => $item->UUID_UNIT,
            'uuid' => $item->UUID_BARANG
        ];
    });

        return view('activityUnit.edit', compact('dailyUnit', 'unit', 'user', 'activity', 'reqBy', 'actual', 'status', 'barang', 'barangKeluar'));
    }

    public function delete($uuid)
    {

        try {
            ActivityUnit::where('UUID', $uuid)->update([
                'STATUSENABLED' => false,
                'DELETE_BY' => Auth::user()->nrp,
            ]);

            BarangKeluar::where('UUID_ACTIVITY_UNIT', $uuid)->update([
                'STATUSENABLED' => false,
                'DELETE_BY' => Auth::user()->nrp,
            ]);

            return redirect()->back()->with('success', 'Data berhasil dihapus.');
        } catch (\Throwable $th) {

            return redirect()->back()->with('info', 'Gagal menghapus data: ' . $th->getMessage());
        }
    }

}
