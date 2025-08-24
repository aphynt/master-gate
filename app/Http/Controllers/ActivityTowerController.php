<?php

namespace App\Http\Controllers;

use App\Models\ActivityTower;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\ListActivity;
use App\Models\ListDescriptionProblem;
use App\Models\ListRequestAt;
use App\Models\ListStatus;
use App\Models\ListTeam;
use App\Models\ListTower;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class ActivityTowerController extends Controller
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

        $tower = DB::table('activity_tower as at')
        ->leftJoin('list_tower as lt', 'at.UUID_TOWER', 'lt.UUID')
        ->leftJoin('list_activity as la', 'at.UUID_ACTIVITY', 'la.UUID')
        ->leftJoin('list_status as ls', 'at.UUID_STATUS', 'ls.UUID')
        ->leftJoin('users as us', 'at.REPORTING', 'us.nrp')
        ->select(
            'at.UUID',
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
        ->where('at.DATE_ACTION', $date)
        ->get();

        foreach ($tower as $act) {
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


        return view('activityTower.index', compact('tower'));
    }

    public function insert()
    {
        $tower = ListTower::where('STATUSENABLED', true)->get();
        $activity = ListActivity::where('STATUSENABLED', true)->get();
        $reqBy = ListRequestAt::where('STATUSENABLED', true)->get();
        $actual = ListDescriptionProblem::where('STATUSENABLED', true)->get();
        $status = ListStatus::where('STATUSENABLED', true)->get();
        $barang = Barang::where('STATUSENABLED', true)->get();
        $user = User::select('UUID', 'name as NAME', 'nama_panggilan as NAMA_PANGGILAN', 'NRP')->where('STATUSENABLED', true)->where('role', '!=', 'ADMIN')->get();

        return view('activityTower.insert', compact('tower', 'user', 'activity', 'reqBy', 'actual', 'status', 'barang'));
    }

    public function post(Request $request)
    {

        $activities = $request->input('data');
        $consumables = $request->filled('CONSUMABLE') ? json_decode($request->input('CONSUMABLE'), true) : [];
        try {
            // Menyimpan tower yang sudah diberi consumable
            $towerConsumableDone = [];

            foreach ($activities as $act) {
                $activity = ActivityTower::create([
                    'UUID' => $act['UUID'],
                    'STATUSENABLED' => true,
                    'UUID_TOWER' => $act['UUID_TOWER'],
                    'DATE_ACTION' => $request->DATE_REPORT,
                    'UUID_ACTIVITY' => $act['UUID_ACTIVITY'],
                    'ACTUAL_PROBLEM' => $act['ACTUAL_PROBLEM'],
                    'ACTION_PROBLEM' => $act['ACTION_PROBLEM'],
                    'START' => normalizeTime($act['START']),
                    'FINISH' => normalizeTime($act['FINISH']),
                    'UUID_STATUS' => $act['STATUS'],
                    'ACTION_BY' => is_array($act['ACTION_BY']) ? implode(',', $act['ACTION_BY']) : $act['ACTION_BY'],
                    'REMARKS' => $act['REMARKS'],
                    'REPORTING' => Auth::user()->nrp,
                    'ADD_BY' => Auth::user()->nrp,
                ]);

                // Cek apakah tower ini sudah diproses untuk barang keluar
                if (!in_array($act['UUID_TOWER'], $towerConsumableDone)) {
                    $filteredConsumables = array_filter($consumables, fn($csm) => $csm['towerUUID'] === $act['UUID_TOWER']);

                    foreach ($filteredConsumables as $csm) {
                        if (!is_array($csm)) {
                            continue;
                        }
                        BarangKeluar::create([
                            'UUID' => (string) Uuid::uuid4()->toString(),
                            'STATUSENABLED' => true,
                            'UUID_BARANG' => $csm['uuid'],
                            'TANGGAL_KELUAR' => $request->DATE_REPORT,
                            'JUMLAH' => $csm['qty'],
                            'PIC' => is_array($act['ACTION_BY']) ? implode(',', $act['ACTION_BY']) : $act['ACTION_BY'],
                            'UUID_ACTIVITY_TOWER' => $activity->UUID, // hubungkan ke activity pertama tower ini
                            'KETERANGAN' => $act['REMARKS'],
                            'REPORTING' => Auth::user()->nrp,
                            'ADD_BY' => Auth::user()->nrp,
                        ]);
                    }

                    // Tandai tower sudah diproses
                    $towerConsumableDone[] = $act['UUID_TOWER'];
                }
            }

            return redirect()->route('activityTower.index')->with('success', 'Semua activity tower berhasil ditambahkan.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('info', 'Terjadi kesalahan: ' . $th->getMessage());
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
            // dd($activities);

            foreach ($activities as $row) {
                $activity = ActivityTower::where('UUID', $row['UUID'])->first();

                if ($activity) {
                    $uuidActivity = $activity->UUID;
                    $uuidTower = $row['UUID_TOWER'];

                    // Proses ACTION_BY jika ada
                    // $cleanedActionBy = [];
                    // if (isset($row['ACTION_BY']) && !empty($row['ACTION_BY'])) {
                    //     $actionByArray = is_array($row['ACTION_BY']) ? $row['ACTION_BY'] : explode(',', $row['ACTION_BY']);
                    //     $cleanedActionBy = array_unique(array_map('trim', $actionByArray));
                    // }

                    $updateData = [
                        'UUID_TOWER' => $uuidTower,
                        'DATE_ACTION' => $row['DATE_REPORT'],
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

                    ActivityTower::where('UUID', $uuidActivity)->update($updateData);

                    BarangKeluar::where('UUID_ACTIVITY_TOWER', $uuidActivity)
                        ->update(['STATUSENABLED' => false]);

                    foreach ($consumables as $group) {
                        if (!is_array($group)) {
                            continue;
                        }
                        foreach ($group as $cons) {
                            if (isset($cons['towerUUID']) && $cons['towerUUID'] == $uuidTower) {
                                BarangKeluar::create([
                                    'UUID' => (string) Uuid::uuid4()->toString(),
                                    'STATUSENABLED' => true,
                                    'UUID_BARANG' => $cons['uuid'],
                                    'TANGGAL_KELUAR' => $row['DATE_REPORT'],
                                    'JUMLAH' => $cons['qty'],
                                    'UUID_ACTIVITY_TOWER' => $uuidActivity,
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

            return redirect()->route('activityTower.index')->with('success', 'Semua data berhasil diperbarui!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('info', 'Terjadi kesalahan: ' . $th->getMessage());
        }


    }

    public function detail(Request $request)
    {
        $idsString = $request->query('ids');

        if (!$idsString) {
            return redirect()->route('activityTower.index')
                ->with('info', 'Tidak ada data yang dipilih untuk dilihat.');
        }

        $ids = explode(',', $idsString);

        $users = DB::table('users')->pluck('nama_panggilan', 'nrp');
        $tower = ListTower::where('STATUSENABLED', true)->get();
        $activity = ListActivity::where('STATUSENABLED', true)->get();
        $reqBy = ListRequestAt::where('STATUSENABLED', true)->get();
        $actual = ListDescriptionProblem::where('STATUSENABLED', true)->get();
        $status = ListStatus::where('STATUSENABLED', true)->get();
        $barang = Barang::where('STATUSENABLED', true)->get();
        $user = User::select('UUID', 'name as NAME', 'nama_panggilan as NAMA_PANGGILAN', 'NRP')->where('STATUSENABLED', true)->where('role', '!=', 'ADMIN')->orderByDesc('nama_panggilan')->get();
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

        $dailyTower = DB::table('activity_tower as at')
            ->leftJoin('list_tower as lt', 'at.UUID_TOWER', 'lt.UUID')
            ->leftJoin('list_activity as la', 'at.UUID_ACTIVITY', 'la.UUID')
            ->leftJoin('list_status as ls', 'at.UUID_STATUS', 'ls.UUID')
            ->leftJoin('users as us', 'at.REPORTING', 'us.nrp')
            ->select(
                'at.UUID',
                'lt.UUID as UUID_TOWER',
                'lt.NAMA as NAMA_TOWER',
                DB::raw("FORMAT(at.DATE_ACTION, 'yyyy-MM-dd') as DATE_REPORT"),
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
            ->get()->map(function ($row) use ($convertPIC) {
                $row->ACTION_BY = $row->ACTION_BY ? $convertPIC($row->ACTION_BY) : null;
                return $row;
            });

        if ($dailyTower->isEmpty()) {
            return redirect()->route('activityTower.index')
                ->with('error', 'Data yang dipilih tidak ditemukan.');
        }

        $activityUUIDs = $dailyTower->pluck('UUID');

        $barangKeluar = DB::table('log_barang_keluar as bk')
        ->leftJoin('log_barang as br', 'bk.UUID_BARANG', 'br.UUID')
        ->leftJoin('activity_tower as at', 'bk.UUID_ACTIVITY_TOWER', 'at.UUID')
        ->leftJoin('list_tower as twr', 'at.UUID_TOWER', 'twr.UUID')
        ->select(
            'twr.UUID as UUID_TOWER',
            'twr.NAMA as NAMA_TOWER',
            'br.UUID as UUID_BARANG',
            'br.ITEM as NAMA_BARANG',
            'bk.JUMLAH',
            'bk.STATUSENABLED'
        )
        ->whereIn('UUID_ACTIVITY_TOWER', $activityUUIDs)->where('bk.STATUSENABLED', true)->get();

        $barangKeluar = $barangKeluar->map(function ($item) {
        return [
            'tower' => $item->NAMA_TOWER,
            'item' => $item->NAMA_BARANG,
            'qty' => $item->JUMLAH,
            'towerUUID' => $item->UUID_TOWER,
            'uuid' => $item->UUID_BARANG
        ];
    });

        return view('activityTower.detail', compact('dailyTower', 'tower', 'user', 'activity', 'reqBy', 'actual', 'status', 'barang', 'barangKeluar'));
    }

    public function edit(Request $request)
    {
        $idsString = $request->query('ids');

        if (!$idsString) {
            return redirect()->route('activityTower.index')
                ->with('info', 'Tidak ada data yang dipilih untuk diedit.');
        }

        $ids = explode(',', $idsString);

        $users = DB::table('users')->pluck('nama_panggilan', 'nrp');
        $tower = ListTower::where('STATUSENABLED', true)->get();
        $activity = ListActivity::where('STATUSENABLED', true)->get();
        $reqBy = ListRequestAt::where('STATUSENABLED', true)->get();
        $actual = ListDescriptionProblem::where('STATUSENABLED', true)->get();
        $status = ListStatus::where('STATUSENABLED', true)->get();
        $barang = Barang::where('STATUSENABLED', true)->get();
        $user = User::select('UUID', 'name as NAME', 'nama_panggilan as NAMA_PANGGILAN', 'NRP')->where('STATUSENABLED', true)->where('role', '!=', 'ADMIN')->orderByDesc('nama_panggilan')->get();

        $dailyTower = DB::table('activity_tower as at')
            ->leftJoin('list_tower as lt', 'at.UUID_TOWER', 'lt.UUID')
            ->leftJoin('list_activity as la', 'at.UUID_ACTIVITY', 'la.UUID')
            ->leftJoin('list_status as ls', 'at.UUID_STATUS', 'ls.UUID')
            ->leftJoin('users as us', 'at.REPORTING', 'us.nrp')
            ->select(
                'at.UUID',
                'lt.UUID as UUID_TOWER',
                'lt.NAMA as NAMA_TOWER',
                DB::raw("FORMAT(at.DATE_ACTION, 'yyyy-MM-dd') as DATE_REPORT"),
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

        if ($dailyTower->isEmpty()) {
            return redirect()->route('activityTower.index')
                ->with('error', 'Data yang dipilih tidak ditemukan.');
        }

        $activityUUIDs = $dailyTower->pluck('UUID');

        $barangKeluar = DB::table('log_barang_keluar as bk')
        ->leftJoin('log_barang as br', 'bk.UUID_BARANG', 'br.UUID')
        ->leftJoin('activity_tower as at', 'bk.UUID_ACTIVITY_TOWER', 'at.UUID')
        ->leftJoin('list_tower as twr', 'at.UUID_TOWER', 'twr.UUID')
        ->select(
            'twr.UUID as UUID_TOWER',
            'twr.NAMA as NAMA_TOWER',
            'br.UUID as UUID_BARANG',
            'br.ITEM as NAMA_BARANG',
            'bk.JUMLAH',
            'bk.STATUSENABLED'
        )
        ->whereIn('UUID_ACTIVITY_TOWER', $activityUUIDs)->where('bk.STATUSENABLED', true)->get();

        $barangKeluar = $barangKeluar->map(function ($item) {
        return [
            'tower' => $item->NAMA_TOWER,
            'item' => $item->NAMA_BARANG,
            'qty' => $item->JUMLAH,
            'towerUUID' => $item->UUID_TOWER,
            'uuid' => $item->UUID_BARANG
        ];
    });

        return view('activityTower.edit', compact('dailyTower', 'tower', 'user', 'activity', 'reqBy', 'actual', 'status', 'barang', 'barangKeluar'));
    }

    public function delete($uuid)
    {

        try {
            ActivityTower::where('UUID', $uuid)->update([
                'STATUSENABLED' => false,
                'DELETE_BY' => Auth::user()->nrp,
            ]);

            BarangKeluar::where('UUID_ACTIVITY_TOWER', $uuid)->update([
                'STATUSENABLED' => false,
                'DELETE_BY' => Auth::user()->nrp,
            ]);

            return redirect()->back()->with('success', 'Data berhasil dihapus.');
        } catch (\Throwable $th) {

            return redirect()->back()->with('info', 'Gagal menghapus data: ' . $th->getMessage());
        }
    }
}
