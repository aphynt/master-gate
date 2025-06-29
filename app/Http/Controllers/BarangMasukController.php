<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class BarangMasukController extends Controller
{
    //
    public function index()
    {

        $users = DB::table('users')->pluck('name', 'nrp');

        $barangMasuk = DB::table('log_barang_masuk as bm')
        ->leftJoin('log_barang as br', 'bm.UUID_BARANG', 'br.UUID')
        ->leftJoin('users as us', 'bm.REPORTING', 'us.nrp')
        ->select(
            'bm.UUID',
            'br.ITEM as NAMA_BARANG',
            'bm.TANGGAL_MASUK',
            'bm.JUMLAH',
            'bm.PIC',
            'bm.KETERANGAN',
            'bm.REPORTING as NRP_REPORTING',
            'us.name as NAMA_REPORTING',
        )->where('bm.STATUSENABLED', true)->get();

        foreach ($barangMasuk as $brm) {
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

        return view('barangMasuk.index', compact('barangMasuk'));
    }

    public function insert()
    {
        $barang = Barang::where('STATUSENABLED', true)->get();
        $user = User::where('STATUSENABLED', true)->where('role', '!=', 'ADMIN')->orderByDesc('nama_panggilan')->get();
        return view('barangMasuk.insert', compact('barang', 'user'));
    }

    public function post(Request $request)
    {
        try {

            $actionByString = is_array($request->PIC) ? implode(',', $request->PIC) : $request->PIC;

            BarangMasuk::insert([
                'UUID'           => (string) Uuid::uuid4()->toString(),
                'STATUSENABLED'  => true,
                'TANGGAL_MASUK'      => $request->TANGGAL_MASUK,
                'UUID_BARANG'      => $request->UUID_BARANG,
                'JUMLAH'      => $request->JUMLAH,
                'PIC'      => $actionByString,
                'KETERANGAN'      => $request->KETERANGAN,
                'REPORTING' => Auth::user()->nrp,
                'ADD_BY' => Auth::user()->nrp,
                'CREATED_AT' => now(),
                'UPDATED_AT' => now(),
            ]);

            return redirect()->route('barangMasuk.index')->with('success', 'Data berhasil disimpan.');
        } catch (\Throwable $th) {
            return redirect()->route('barangMasuk.index')->with('info', 'Gagal menyimpan data: ' . $th->getMessage());
        }
    }

    public function edit($uuid)
    {
        $users = DB::table('users')->pluck('name', 'nrp');
        $barang = Barang::where('STATUSENABLED', true)->get();
        $user = User::where('STATUSENABLED', true)->where('role', '!=', 'ADMIN')->orderByDesc('nama_panggilan')->get();

        $barangMasuk = DB::table('log_barang_masuk as bm')
        ->leftJoin('log_barang as br', 'bm.UUID_BARANG', 'br.UUID')
        ->leftJoin('users as us', 'bm.REPORTING', 'us.nrp')
        ->select(
            'bm.UUID',
            'bm.UUID_BARANG',
            'br.ITEM as NAMA_BARANG',
            'bm.TANGGAL_MASUK',
            'bm.JUMLAH',
            'bm.PIC',
            'bm.KETERANGAN',
            'bm.REPORTING as NRP_REPORTING',
            'us.name as NAMA_REPORTING',
        )->where('bm.STATUSENABLED', true)->where('bm.UUID', $uuid)->first();



        $nrps = explode(',', $barangMasuk->PIC);
            $names = [];

            foreach ($nrps as $nrp) {
                $nrp = trim($nrp);
                if (isset($users[$nrp])) {
                    $names[] = $users[$nrp];
                }
            }

            $barangMasuk->NAMA_PIC = implode(', ', $names);

        return view('barangMasuk.edit', compact('barangMasuk', 'barang', 'user'));
    }

    public function update(Request $request, $uuid)
    {
        try {
            BarangMasuk::where('UUID', $uuid)->update([
                'JUMLAH' => $request->JUMLAH,
                'KETERANGAN' => $request->KETERANGAN,
                'UPDATED_AT' => now(),
                'UPDATED_BY' => Auth::user()->nrp,
            ]);

            return redirect()->route('barangMasuk.index')->with('success', 'Data berhasil diupdate.');
        } catch (\Throwable $th) {

            return redirect()->route('barangMasuk.index')->with('info', 'Gagal mengupdate data: ' . $th->getMessage());
        }
    }

    public function delete($uuid)
    {

        try {
            BarangMasuk::where('UUID', $uuid)->update([
                'STATUSENABLED' => false,
                'DELETE_BY' => Auth::user()->nrp,
            ]);

            return redirect()->back()->with('success', 'Data berhasil dihapus.');
        } catch (\Throwable $th) {

            return redirect()->back()->with('info', 'Gagal menghapus data: ' . $th->getMessage());
        }
    }
}
