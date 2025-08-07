<?php

namespace App\Http\Controllers;

use App\Models\ListTeam;
use App\Models\User;
use App\Models\WeeklyPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class WeeklyPlanController extends Controller
{
    //
    public function index(Request $request)
    {
        if(!empty($request->DATE_REPORT)){
            $tanggal = $request->DATE_REPORT;
        }else{
            $tanggal = Carbon::today()->format('Y-m-d');
        }
        $users = DB::table('users')->pluck('name', 'nrp');

        $convertPIC = function ($picString) use ($users) {
            $nrps = explode(',', $picString);
            $names = [];

            foreach ($nrps as $nrp) {
                $nrp = trim($nrp);
                if (isset($users[$nrp])) {
                    $names[] = $users[$nrp];
                }
            }

            return implode(', ', $names);
        };


        $tanggalSekarang = Carbon::parse($tanggal);
        $selisihHari = ($tanggalSekarang->dayOfWeek - 3 + 7) % 7;
        $startDate = $tanggalSekarang->copy()->subDays($selisihHari);
        $endDate = $startDate->copy()->addDays(6);

        $weekly = DB::table('PLAN_WEEKLY as pw')
        ->leftJoin('LIST_TEAM as lt', 'pw.TEAM', 'lt.UUID')
        ->leftJoin('users as us', 'pw.REPORTING', 'us.nrp')
        ->select(
            'pw.UUID',
            'pw.STARTDATE',
            'pw.ENDDATE',
            'pw.WORK_ITEMS',
            'pw.ACTION_BY',
            'lt.NAMA as TEAM',
            'us.nrp as REPORTING',
        )
        ->where('pw.STATUSENABLED', true)
            ->whereDate('pw.STARTDATE', '>=', $startDate)
            ->whereDate('pw.ENDDATE', '<=', $endDate)
            ->get()
            ->map(function ($row) use ($convertPIC) {
                $row->ACTION_BY = $row->ACTION_BY ? $convertPIC($row->ACTION_BY) : null;
                return $row;
            });

        return view('weeklyPlan.index', compact('weekly'));
    }

    public function insert()
    {
        $team = ListTeam::where('STATUSENABLED', true)->where('ID', '!=', 1)->get();
        $user = User::select('UUID', 'name as NAME', 'nama_panggilan as NAMA_PANGGILAN', 'NRP')->where('STATUSENABLED', true)->where('role', '!=', 'ADMIN')->orderByDesc('nama_panggilan')->get();

        return view('weeklyPlan.insert', compact('team', 'user'));
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
                    empty($row['STARTDATE']) ||
                    empty($row['ENDDATE']) ||
                    empty($row['WORK_ITEMS']) ||
                    empty($row['ACTION_BY'])
                ) {
                    continue;
                }

                $actionByString = is_array($row['ACTION_BY']) ? implode(',', $row['ACTION_BY']) : $row['ACTION_BY'];

                WeeklyPlan::insert([
                    'UUID'           => (string) Uuid::uuid4()->toString(),
                    'STATUSENABLED'  => true,
                    'STARTDATE' => Carbon::parse($row['STARTDATE'])->format('Y-m-d'),
                    'ENDDATE' => Carbon::parse($row['ENDDATE'])->format('Y-m-d'),
                    'WORK_ITEMS' => $row['WORK_ITEMS'],
                    'TEAM' => $row['TEAM'],
                    'ACTION_BY'      => $actionByString,
                    'REPORTING' => Auth::user()->nrp,
                    'CREATED_AT' => now(),
                    'UPDATED_AT' => now(),
                ]);
            }

            DB::commit();
            return redirect()->route('weeklyPlan.index')->with('success', 'Data berhasil disimpan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('weeklyPlan.index')->with('info', 'Gagal menyimpan data: ' . $th->getMessage());
        }
    }

    public function delete($uuid)
    {
        try {
            WeeklyPlan::where('UUID', $uuid)->update([
                'STATUSENABLED' => false,
                'DELETE_BY' => Auth::user()->nrp,
            ]);

            return redirect()->back()->with('success', 'Data berhasil dihapus.');
        } catch (\Throwable $th) {

            return redirect()->back()->with('info', 'Gagal menghapus data: ' . $th->getMessage());
        }
    }
}
