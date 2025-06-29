<?php

namespace App\Http\Controllers;

use App\Models\ListActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class ListActivityController extends Controller
{
    //
    public function index()
    {
        $listActivity = ListActivity::all();
        return view('listActivity.index', compact('listActivity'));
    }

    public function insert(Request $request)
    {

        try {
            ListActivity::create([
                'UUID' => (string) Uuid::uuid4()->toString(),
                'STATUSENABLED' => (int) $request->STATUSENABLED,
                'KETERANGAN' => $request->KETERANGAN,
                'ADD_BY' => Auth::user()->nrp,
                // 'CREATED_AT' => now(),
                // 'UPDATED_AT' => now(),
            ]);

            return redirect()->back()->with('success', 'Activity berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->back()->with('info', 'Terjadi kesalahan: '. $th->getMessage());
        }
    }

    public function update(Request $request, $uuid)
    {

        try {

            ListActivity::where('UUID', $uuid)->update([
                'STATUSENABLED' => (int) $request->STATUSENABLED,
                'KETERANGAN' => $request->KETERANGAN,
                'UPDATED_BY' => Auth::user()->nrp,
                // 'UPDATED_AT' => now(),
            ]);

            return redirect()->back()->with('success', 'Activity berhasil diupdate');
        } catch (\Throwable $th) {
            return redirect()->back()->with('info', 'Terjadi kesalahan: '. $th->getMessage());
        }
    }
}
