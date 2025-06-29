<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class BarangKeluarController extends Controller
{
    //
    public function index()
    {

        $users = DB::table('users')->pluck('name', 'nrp');

        $barangKeluar = DB::table('log_barang_keluar as bk')
        ->leftJoin('log_barang as br', 'bk.UUID_BARANG', 'br.UUID')
        ->leftJoin('users as us', 'bk.REPORTING', 'us.nrp')
        ->select(
            'bk.UUID',
            'br.ITEM as NAMA_BARANG',
            'bk.TANGGAL_KELUAR',
            'bk.JUMLAH',
            'bk.PIC',
            'bk.KETERANGAN',
            'bk.REPORTING as NRP_REPORTING',
            'us.name as NAMA_REPORTING',
        )->where('bk.STATUSENABLED', true)->get();

        foreach ($barangKeluar as $brm) {
            $nrps = explode(',', $brm->PIC);
            $names = [];

            foreach ($nrps as $nrp) {
                $nrp = trim($nrp);
                if (isset($users[$nrp])) {
                    $names[] = $users[$nrp];
                }
            }

            $brm->PIC = implode(', ', $names);
        }

        return view('barangKeluar.index', compact('barangKeluar'));
    }

    public function insert()
    {
        $barang = Barang::where('STATUSENABLED', true)->get();
        $user = User::where('STATUSENABLED', true)->where('role', '!=', 'ADMIN')->get();
        return view('barangKeluar.insert', compact('barang', 'user'));
    }

    public function post(Request $request)
    {
        try {

            $actionByString = is_array($request->PIC) ? implode(',', $request->PIC) : $request->PIC;

            BarangKeluar::insert([
                'UUID'           => (string) Uuid::uuid4()->toString(),
                'STATUSENABLED'  => true,
                'TANGGAL_KELUAR'      => $request->TANGGAL_KELUAR,
                'UUID_BARANG'      => $request->UUID_BARANG,
                'JUMLAH'      => $request->JUMLAH,
                'PIC'      => $actionByString,
                'KETERANGAN'      => $request->KETERANGAN,
                'REPORTING' => Auth::user()->nrp,
                'ADD_BY' => Auth::user()->nrp,
                'CREATED_AT' => now(),
                'UPDATED_AT' => now(),
            ]);

            return redirect()->route('barangKeluar.index')->with('success', 'Data berhasil disimpan.');
        } catch (\Throwable $th) {
            return redirect()->route('barangKeluar.index')->with('info', 'Gagal menyimpan data: ' . $th->getMessage());
        }
    }

    public function edit($uuid)
    {
        $users = DB::table('users')->pluck('name', 'nrp');
        $barang = Barang::where('STATUSENABLED', true)->get();
        $user = User::where('STATUSENABLED', true)->where('role', '!=', 'ADMIN')->get();

        $barangKeluar = DB::table('log_barang_keluar as bk')
        ->leftJoin('log_barang as br', 'bk.UUID_BARANG', 'br.UUID')
        ->leftJoin('users as us', 'bk.REPORTING', 'us.nrp')
        ->select(
            'bk.UUID',
            'bk.UUID_BARANG',
            'br.ITEM as NAMA_BARANG',
            'bk.TANGGAL_KELUAR',
            'bk.JUMLAH',
            'bk.PIC',
            'bk.KETERANGAN',
            'bk.REPORTING as NRP_REPORTING',
            'us.name as NAMA_REPORTING',
        )->where('bk.STATUSENABLED', true)->where('bk.UUID', $uuid)->first();



        $nrps = explode(',', $barangKeluar->PIC);
            $names = [];

            foreach ($nrps as $nrp) {
                $nrp = trim($nrp);
                if (isset($users[$nrp])) {
                    $names[] = $users[$nrp];
                }
            }

            $barangKeluar->NAMA_PIC = implode(', ', $names);

        return view('barangKeluar.edit', compact('barangKeluar', 'barang', 'user'));
    }

    public function update(Request $request, $uuid)
    {
        try {
            BarangKeluar::where('UUID', $uuid)->update([
                'JUMLAH' => $request->JUMLAH,
                'KETERANGAN' => $request->KETERANGAN,
                'UPDATED_AT' => now(),
                'UPDATED_BY' => Auth::user()->nrp,
            ]);

            return redirect()->route('barangKeluar.index')->with('success', 'Data berhasil diupdate.');
        } catch (\Throwable $th) {

            return redirect()->route('barangKeluar.index')->with('info', 'Gagal mengupdate data: ' . $th->getMessage());
        }
    }

    public function delete($uuid)
    {

        try {
            BarangKeluar::where('UUID', $uuid)->update([
                'STATUSENABLED' => false,
                'DELETE_BY' => Auth::user()->nrp,
            ]);

            return redirect()->back()->with('success', 'Data berhasil dihapus.');
        } catch (\Throwable $th) {

            return redirect()->back()->with('info', 'Gagal menghapus data: ' . $th->getMessage());
        }
    }

}
