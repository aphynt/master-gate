<?php

namespace App\Http\Controllers;

use App\Models\ListStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class ListStatusController extends Controller
{
    //
    public function index()
    {
        $listStatus = ListStatus::all();
        return view('listStatus.index', compact('listStatus'));
    }

    public function insert(Request $request)
    {

        try {
            ListStatus::create([
                'UUID' => (string) Uuid::uuid4()->toString(),
                'STATUSENABLED' => (int) $request->STATUSENABLED,
                'KETERANGAN' => $request->KETERANGAN,
                'ADD_BY' => Auth::user()->nrp,
                // 'CREATED_AT' => now(),
                // 'UPDATED_AT' => now(),
            ]);

            return redirect()->back()->with('success', 'Status berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->back()->with('info', 'Terjadi kesalahan: '. $th->getMessage());
        }
    }

    public function update(Request $request, $uuid)
    {

        try {

            ListStatus::where('UUID', $uuid)->update([
                'STATUSENABLED' => (int) $request->STATUSENABLED,
                'KETERANGAN' => $request->KETERANGAN,
                'UPDATED_BY' => Auth::user()->nrp,
                // 'UPDATED_AT' => now(),
            ]);

            return redirect()->back()->with('success', 'Status berhasil diupdate');
        } catch (\Throwable $th) {
            return redirect()->back()->with('info', 'Terjadi kesalahan: '. $th->getMessage());
        }
    }
}
