<?php

namespace App\Http\Controllers;

use App\Models\ListTeam;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ActivityPergantianBarang;
use App\Models\Barang;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class ActivityPergantianBarangController extends Controller
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

        $team = ListTeam::where('STATUSENABLED', true)->get();

        $activity = DB::table('ACTIVITY_PERGANTIAN_BARANG as ganti')
            ->leftJoin('LOG_BARANG as barang1', 'ganti.UUID_BARANG_DILEPAS', 'barang1.UUID')
            ->leftJoin('LOG_BARANG as barang2', 'ganti.UUID_BARANG_DIPASANG', 'barang2.UUID')
            ->leftJoin('users as us', 'ganti.REPORTING', 'us.nrp')
            ->select(
                'ganti.UUID',
                'ganti.STATUSENABLED',
                DB::raw("FORMAT(ganti.TANGGAL_PELEPASAN, 'yyyy-MM-dd') as TANGGAL_PELEPASAN"),
                'barang1.ITEM as PERANGKAT_DILEPAS',
                'ganti.SN_BARANG_DILEPAS',
                'ganti.POSISI_BARANG',
                'ganti.REMARKS',
                'ganti.ACTION_BY',
                'us.name as REPORTING',
                'ganti.REPORTING as NRP_REPORTING',
            )
            ->where('ganti.STATUSENABLED', true)
            ->where('ganti.TANGGAL_PELEPASAN', $date)
            ->get();


        foreach ($activity as $act) {
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

        return view('activityPergantianBarang.index', compact('activity', 'team'));
    }

    public function insert()
    {
        $team = ListTeam::where('STATUSENABLED', true)->get();
        $barang = Barang::where('STATUSENABLED', true)->get();
        $user = User::select('UUID', 'name as NAME', 'nama_panggilan as NAMA_PANGGILAN', 'NRP')->where('STATUSENABLED', true)->where('role', '!=', 'ADMIN')->orderByDesc('nama_panggilan')->get();

        return view('activityPergantianBarang.insert', compact('team', 'user', 'barang'));
    }

    public function edit($uuid)
    {
        $pergantianBarang = DB::table('ACTIVITY_PERGANTIAN_BARANG as ganti')
            ->leftJoin('LOG_BARANG as barang1', 'ganti.UUID_BARANG_DILEPAS', 'barang1.UUID')
            ->leftJoin('LOG_BARANG as barang2', 'ganti.UUID_BARANG_DIPASANG', 'barang2.UUID')
            ->leftJoin('users as us', 'ganti.REPORTING', 'us.nrp')
            ->select(
                'ganti.*',
                'barang1.UUID as UUID_PERANGKAT_DILEPAS',
                'barang1.ITEM as PERANGKAT_DILEPAS',
                'barang2.UUID as UUID_PERANGKAT_DIPASANG',
                'barang2.ITEM as PERANGKAT_DIPASANG',
                'us.name as REPORTING',
            )
            ->where('ganti.STATUSENABLED', true)
            ->where('ganti.UUID', $uuid)
            ->first();

        if (!$pergantianBarang) {
            return redirect()->back()
                ->with('info', 'Data yang dipilih tidak ditemukan.');
        }

        $users = DB::table('users')->pluck('name', 'nrp');
        $nrps = explode(',', $pergantianBarang->ACTION_BY);
        $names = [];

        foreach ($nrps as $nrp) {
            $nrp = trim($nrp);
            if (isset($users[$nrp])) {
                $names[] = $users[$nrp];
            }
        }

        $pergantianBarang->ACTION_BY = implode(', ', $names);

        $team = ListTeam::where('STATUSENABLED', true)->get();
        $barang = Barang::where('STATUSENABLED', true)->get();
        $user = User::select('UUID', 'name as NAME', 'nama_panggilan as NAMA_PANGGILAN', 'NRP')->where('STATUSENABLED', true)->where('role', '!=', 'ADMIN')->orderByDesc('nama_panggilan')->get();

        return view('activityPergantianBarang.edit', compact('team', 'user', 'barang', 'pergantianBarang'));
    }

    public function post(Request $request)
    {

        DB::beginTransaction();
        try {
            ActivityPergantianBarang::insert([
                'UUID'                      => (string) Uuid::uuid4()->toString(),
                'STATUSENABLED'             => true,
                'UUID_BARANG_DILEPAS'       => $request->UUID_BARANG_DILEPAS,
                'SN_BARANG_DILEPAS'         => $request->SN_BARANG_DILEPAS,
                'POSISI_AWAL'               => $request->POSISI_AWAL,
                'TANGGAL_PELEPASAN'         => $request->TANGGAL_PELEPASAN,
                'UUID_BARANG_DIPASANG'      => $request->UUID_BARANG_DIPASANG,
                'SN_BARANG_DIPASANG'        => $request->SN_BARANG_DIPASANG,
                'TUJUAN_PEMASANGAN'         => $request->TUJUAN_PEMASANGAN,
                'TANGGAL_PEMASANGAN'        => $request->TANGGAL_PEMASANGAN,
                'REMARKS'                   => $request->REMARKS,
                'POSISI_BARANG'                   => $request->POSISI_BARANG,
                'REPORTING'                 => Auth::user()->nrp,
                'ACTION_BY'                 => is_array($request->ACTION_BY) ? implode(',', $request->ACTION_BY) : $request->ACTION_BY,
                'CREATED_AT'                => now(),
                'UPDATED_AT'                => now(),
                'ADD_BY'                    => Auth::user()->nrp,
            ]);

            DB::commit();
            return redirect()->route('activityPergantianBarang.index')->with('success', 'Data berhasil disimpan.');
        } catch (\Throwable $th) {

            DB::rollBack();
            return redirect()->route('activityPergantianBarang.insert')->with('info', 'Gagal menyimpan data: ' . $th->getMessage());
        }
    }

    public function update(Request $request, $uuid)
    {
        DB::beginTransaction();
        try {
            ActivityPergantianBarang::where('UUID', $uuid)->update([
                'STATUSENABLED'             => true,
                'UUID_BARANG_DILEPAS'       => $request->UUID_BARANG_DILEPAS,
                'SN_BARANG_DILEPAS'         => $request->SN_BARANG_DILEPAS,
                'POSISI_AWAL'               => $request->POSISI_AWAL,
                'TANGGAL_PELEPASAN'         => $request->TANGGAL_PELEPASAN,
                'UUID_BARANG_DIPASANG'      => $request->UUID_BARANG_DIPASANG,
                'SN_BARANG_DIPASANG'        => $request->SN_BARANG_DIPASANG,
                'TUJUAN_PEMASANGAN'         => $request->TUJUAN_PEMASANGAN,
                'TANGGAL_PEMASANGAN'        => $request->TANGGAL_PEMASANGAN,
                'REMARKS'                   => $request->REMARKS,
                'POSISI_BARANG'                   => $request->POSISI_BARANG,
                'REPORTING'                 => Auth::user()->nrp,
                'UPDATED_BY'                    => Auth::user()->nrp,
            ]);

            DB::commit();
            return redirect()->route('activityPergantianBarang.index')->with('success', 'Data berhasil diupdate.');
        } catch (\Throwable $th) {

            DB::rollBack();
            return redirect()->back()->with('info', 'Gagal mengupdate data: ' . $th->getMessage());
        }

    }

    public function delete($uuid)
    {

        try {
            ActivityPergantianBarang::where('UUID', $uuid)->update([
                'STATUSENABLED' => false,
                'DELETE_BY' => Auth::user()->nrp,
            ]);

            return redirect()->back()->with('success', 'Data berhasil dihapus.');
        } catch (\Throwable $th) {

            return redirect()->back()->with('info', 'Gagal menghapus data: ' . $th->getMessage());
        }
    }

}
