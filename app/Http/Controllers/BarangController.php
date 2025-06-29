<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class BarangController extends Controller
{
    //
    public function index()
    {
        $barang = Barang::where('STATUSENABLED', true)->get();
        return view('barang.index', compact('barang'));
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
