<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class BarangController extends Controller
{
    //
    public function index()
    {
        $barang = Barang::where('STATUSENABLED', true)->get();


        $barangMasuk = DB::table('LOG_BARANG_MASUK')
        ->select('UUID_BARANG', DB::raw('SUM(JUMLAH) as total_masuk'))
        ->where('STATUSENABLED', true)
        ->groupBy('UUID_BARANG')
        ->get()
        ->keyBy('UUID_BARANG');



        $barangKeluar = DB::table('LOG_BARANG_KELUAR')
        ->select('UUID_BARANG', DB::raw('SUM(JUMLAH) as total_keluar'), 'UUID_ACTIVITY_TOWER', 'UUID_ACTIVITY_UNIT', 'UUID_ACTIVITY_ADDITIONAL')
        ->where('STATUSENABLED', true)
        ->groupBy('UUID_BARANG', 'UUID_ACTIVITY_TOWER', 'UUID_ACTIVITY_UNIT', 'UUID_ACTIVITY_ADDITIONAL')
        ->get()
        ->keyBy('UUID_BARANG');


        return view('barang.index', compact('barang', 'barangMasuk', 'barangKeluar'));
    }

    public function post(Request $request)
    {
        try {
            Barang::insert([
                'UUID'           => (string) Uuid::uuid4()->toString(),
                'STATUSENABLED'  => true,
                'ITEM'      => $request->ITEM,
                'DESCRIPTION'      => $request->DESCRIPTION,
                'STATUS'      => $request->STATUS,
                'STOK_AKHIR'      => $request->STOK_AKHIR,
                'ADD_BY' => Auth::user()->nrp,
                'CREATED_AT' => now(),
                'UPDATED_AT' => now(),
            ]);

            return redirect()->back()->with('success', 'Data berhasil disimpan.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('info', 'Gagal menyimpan data: ' . $th->getMessage());
        }
    }

    public function delete($uuid)
    {
        try {
            Barang::where('UUID', $uuid)->update([
                'STATUSENABLED' => false,
                'DELETE_BY' => Auth::user()->nrp,
            ]);

            return redirect()->back()->with('success', 'Data berhasil dihapus.');
        } catch (\Throwable $th) {

            return redirect()->back()->with('info', 'Gagal menghapus data: ' . $th->getMessage());
        }
    }
}
