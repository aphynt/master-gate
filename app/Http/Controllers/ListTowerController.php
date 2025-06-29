<?php

namespace App\Http\Controllers;

use App\Models\ListTower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class ListTowerController extends Controller
{
    //
    public function index()
    {
        $listTower = ListTower::all();
        return view('listTower.index', compact('listTower'));
    }

    public function insert(Request $request)
    {

        try {
            ListTower::create([
                'UUID' => (string) Uuid::uuid4()->toString(),
                'STATUSENABLED' => (int) $request->STATUSENABLED,
                'NAMA' => $request->NAMA,
                'ADD_BY' => Auth::user()->nrp,
                // 'CREATED_AT' => now(),
                // 'UPDATED_AT' => now(),
            ]);

            return redirect()->back()->with('success', 'Tower berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->back()->with('info', 'Terjadi kesalahan: '. $th->getMessage());
        }
    }

    public function update(Request $request, $uuid)
    {

        try {

            ListTower::where('UUID', $uuid)->update([
                'STATUSENABLED' => (int) $request->STATUSENABLED,
                'NAMA' => $request->NAMA,
                'UPDATED_BY' => Auth::user()->nrp,
                // 'UPDATED_AT' => now(),
            ]);

            return redirect()->back()->with('success', 'Tower berhasil diupdate');
        } catch (\Throwable $th) {
            return redirect()->back()->with('info', 'Terjadi kesalahan: '. $th->getMessage());
        }
    }
}
