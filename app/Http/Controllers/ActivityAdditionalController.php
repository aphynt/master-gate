<?php

namespace App\Http\Controllers;

use App\Models\ActivityAdditional;
use App\Models\ListTeam;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class ActivityAdditionalController extends Controller
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

        $activity = DB::table('activity_additional as add')
            ->leftJoin('list_team as team', 'add.UUID_TEAM', 'team.UUID')
            ->leftJoin('users as us', 'add.REPORTING', 'us.nrp')
            ->select(
                'add.UUID',
                'add.STATUSENABLED',
                'team.NAMA as NAMA_TEAM',
                'add.START',
                'add.FINISH',
                'add.ACTION_PROBLEM',
                'add.ACTION_BY',
                'us.name as REPORTING',
                'add.REPORTING as NRP_REPORTING',
                'add.DATE_REPORT'
            )
            ->where('add.STATUSENABLED', true)
            ->where('add.DATE_REPORT', $date)
            ->get();


        foreach ($activity as $act) {
            $nrps = explode(',', $act->ACTION_BY);
            $names = [];

            foreach ($nrps as $nrp) {
                $nrp = trim($nrp);
                if (isset($users[$nrp])) {
                    $names[] = $users[$nrp];
                }
            }

            $act->ACTION_BY = implode(', ', $names);
        }

        return view('activityAdditional.index', compact('activity'));
    }

    public function insert()
    {
        $team = ListTeam::where('STATUSENABLED', true)->get();
        $user = User::select('UUID', 'name as NAME', 'nama_panggilan as NAMA_PANGGILAN', 'NRP')->where('STATUSENABLED', true)->where('role', '!=', 'ADMIN')->orderByDesc('nama_panggilan')->get();

        return view('activityAdditional.insert', compact('team', 'user'));
    }

    public function post(Request $request)
    {
        // dd($request->all());
        $data = $request->input('data');

        if (!$data || !is_array($data)) {
            return redirect()->back()->with('error', 'Data tidak valid.');
        }

        DB::beginTransaction();
        try {
            foreach ($data as $row) {
                if (
                    empty($row['TEAM']) ||
                    empty($row['START']) ||
                    empty($row['FINISH']) ||
                    empty($row['ACTION_PROBLEM']) ||
                    empty($row['ACTION_BY'])
                ) {
                    continue;
                }

                $actionByString = is_array($row['ACTION_BY']) ? implode(',', $row['ACTION_BY']) : $row['ACTION_BY'];

                ActivityAdditional::insert([
                    'UUID'           => (string) Uuid::uuid4()->toString(),
                    'STATUSENABLED'  => true,
                    'UUID_TEAM'      => $row['TEAM'],
                    'START'          => normalizeTime($row['START']),
                    'FINISH'         => normalizeTime($row['FINISH']),
                    'ACTION_PROBLEM' => $row['ACTION_PROBLEM'],
                    'ACTION_BY'      => $actionByString,
                    'REPORTING' => Auth::user()->nrp,
                    'DATE_REPORT' => $request->DATE_REPORT,
                    'CREATED_AT' => now(),
                    'UPDATED_AT' => now(),
                ]);
            }

            DB::commit();
            return redirect()->route('activityAdditional.index')->with('success', 'Data berhasil disimpan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('activityAdditional.index')->with('info', 'Gagal menyimpan data: ' . $th->getMessage());
        }
    }

    public function update(Request $request, $uuid)
    {

        try {
            ActivityAdditional::where('UUID', $uuid)->update([
                'START'          => normalizeTime($request->START),
                'FINISH'         => normalizeTime($request->FINISH),
                'ACTION_PROBLEM' => $request->ACTION_PROBLEM,
                // 'UPDATED_AT' => now(),
            ]);

            return redirect()->back()->with('success', 'Data berhasil diupdate.');
        } catch (\Throwable $th) {

            return redirect()->back()->with('info', 'Gagal mengupdate data: ' . $th->getMessage());
        }
    }

    public function delete($uuid)
    {

        try {
            ActivityAdditional::where('UUID', $uuid)->update([
                'STATUSENABLED' => false,
                'DELETE_BY' => Auth::user()->nrp,
            ]);

            return redirect()->back()->with('success', 'Data berhasil dihapus.');
        } catch (\Throwable $th) {

            return redirect()->back()->with('info', 'Gagal menghapus data: ' . $th->getMessage());
        }
    }
}
