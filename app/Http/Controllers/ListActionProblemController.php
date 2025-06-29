<?php

namespace App\Http\Controllers;

use App\Models\ListActionProblem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class ListActionProblemController extends Controller
{
    public function index()
    {
        $listActionProblem = ListActionProblem::all();
        return view('listActionProblem.index', compact('listActionProblem'));
    }

    public function insert(Request $request)
    {

        try {
            ListActionProblem::create([
                'UUID' => (string) Uuid::uuid4()->toString(),
                'STATUSENABLED' => (int) $request->STATUSENABLED,
                'KETERANGAN' => $request->KETERANGAN,
                'ADD_BY' => Auth::user()->nrp,
                // 'CREATED_AT' => now(),
                // 'UPDATED_AT' => now(),
            ]);

            return redirect()->back()->with('success', 'Action Problem berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->back()->with('info', 'Terjadi kesalahan: '. $th->getMessage());
        }
    }

    public function update(Request $request, $uuid)
    {

        try {

            ListActionProblem::where('UUID', $uuid)->update([
                'STATUSENABLED' => (int) $request->STATUSENABLED,
                'KETERANGAN' => $request->KETERANGAN,
                'UPDATED_BY' => Auth::user()->nrp,
                // 'UPDATED_AT' => now(),
            ]);

            return redirect()->back()->with('success', 'Action Problem berhasil diupdate');
        } catch (\Throwable $th) {
            return redirect()->back()->with('info', 'Terjadi kesalahan: '. $th->getMessage());
        }
    }
}
