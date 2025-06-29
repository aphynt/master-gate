<?php

namespace App\Http\Controllers;

use App\Models\ListUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class ListUnitController extends Controller
{
    //
    public function index()
    {
        $listUnit = ListUnit::all();
        return view('listUnit.index', compact('listUnit'));
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
