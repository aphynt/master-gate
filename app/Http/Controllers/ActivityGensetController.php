<?php

namespace App\Http\Controllers;

use App\Models\ActivityGenset;
use App\Models\ListTower;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class ActivityGensetController extends Controller
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

        $activity = DB::table('activity_genset as gen')
            ->leftJoin('users as us', 'gen.REPORTING', 'us.nrp')
            ->leftJoin('list_tower as twr', 'gen.UUID_TOWER', 'twr.UUID')
            ->select(
                'gen.UUID',
                'gen.STATUSENABLED',
                'gen.DATE_REPORT',
                'twr.NAMA as NAMA_TOWER',
                'twr.NO_GENSET',
                'gen.KEGIATAN',
                'gen.START',
                'gen.FINISH',
                'gen.FUEL',
                'us.nrp as NRP_REPORTING',
                'us.name as REPORTING',
            )
            ->where('gen.STATUSENABLED', true)
            ->where('gen.DATE_REPORT', $date)
            ->get();


        return view('activityGenset.index', compact('activity'));
    }

    public function insert()
    {
        $tower = ListTower::where('STATUSENABLED', true)->get();
        $user = User::select('UUID', 'name as NAME', 'nama_panggilan as NAMA_PANGGILAN', 'NRP')->where('STATUSENABLED', true)->where('role', '!=', 'ADMIN')->orderByDesc('nama_panggilan')->get();

        return view('activityGenset.insert', compact('tower', 'user'));
    }

    public function post(Request $request)
    {
        // dd($request->all());

        try {
            ActivityGenset::insert([
                'UUID'           => (string) Uuid::uuid4()->toString(),
                'STATUSENABLED'  => true,
                'DATE_REPORT'      => $request->DATE_REPORT,
                'UUID_TOWER'      => $request->UUID_TOWER,
                'KEGIATAN'      => $request->KEGIATAN,
                'START'          => normalizeTime($request->START),
                'FINISH'         => normalizeTime($request->FINISH),
                'FUEL'      => $request->FUEL,
                'REMARKS'      => $request->REMARKS,
                'REPORTING' => Auth::user()->nrp,
                'ACTION_BY' => is_array($request->ACTION_BY) ? implode(',', $request->ACTION_BY) : $request->ACTION_BY,
                'CREATED_AT' => now(),
                'UPDATED_AT' => now(),
            ]);

            return redirect()->route('activityGenset.index')->with('success', 'Data berhasil disimpan.');
        } catch (\Throwable $th) {
            return redirect()->route('activityGenset.index')->with('info', 'Gagal menyimpan data: ' . $th->getMessage());
        }
    }

    public function edit($uuid)
    {
        $tower = ListTower::where('STATUSENABLED', true)->get();

        $activity = DB::table('activity_genset as gen')
            ->leftJoin('users as us', 'gen.REPORTING', 'us.nrp')
            ->leftJoin('list_tower as twr', 'gen.UUID_TOWER', 'twr.UUID')
            ->select(
                'gen.UUID',
                'gen.STATUSENABLED',
                'gen.DATE_REPORT',
                'twr.UUID as UUID_TOWER',
                'twr.NAMA as NAMA_TOWER',
                'twr.NO_GENSET',
                'gen.KEGIATAN',
                'gen.START',
                'gen.FINISH',
                'gen.FUEL',
                'gen.REMARKS',
                'us.name as REPORTING',
            )
            ->where('gen.STATUSENABLED', true)
            ->where('gen.UUID', $uuid)
            ->first();

        return view('activityGenset.edit', compact('activity', 'tower'));
    }

    public function update(Request $request, $uuid)
    {

        try {
            ActivityGenset::where('UUID', $uuid)->update([
                'KEGIATAN'      => $request->KEGIATAN,
                'START'          => normalizeTime($request->START),
                'FINISH'         => normalizeTime($request->FINISH),
                'FUEL'      => $request->FUEL,
                'ACTION_BY' => is_array($request->ACTION_BY) ? implode(',', $request->ACTION_BY) : $request->ACTION_BY,
                'REMARKS'      => $request->REMARKS,
                // 'UPDATED_AT' => now(),
            ]);

            return redirect()->route('activityGenset.index')->with('success', 'Data berhasil diupdate.');
        } catch (\Throwable $th) {

            return redirect()->route('activityGenset.index')->with('info', 'Gagal mengupdate data: ' . $th->getMessage());
        }
    }

    public function delete($uuid)
    {

        try {
            ActivityGenset::where('UUID', $uuid)->update([
                'STATUSENABLED' => false,
                'DELETE_BY' => Auth::user()->nrp,
            ]);

            return redirect()->back()->with('success', 'Data berhasil dihapus.');
        } catch (\Throwable $th) {

            return redirect()->back()->with('info', 'Gagal menghapus data: ' . $th->getMessage());
        }
    }
}
