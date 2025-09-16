@include('layout.head', ['title' => 'Weekly Summary'])
@include('layout.sidebar')
@include('layout.header')
<div class="page-container">

    <div class="page-title-box">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="flex-grow-1">
                <h4 class="font-18 mb-0">Weekly Summary</h4>
            </div>

            <form action="" method="GET" class="d-flex align-items-center gap-2">
                <input type="text" id="basic-datepicker" class="form-control" name="DATE_REPORT"
                    style="width: 160px;" value="{{ request('DATE_REPORT') }}">

                <button type="submit" name="action_type" value="show" class="btn btn-outline-primary">
                    Show Report
                </button>

                <button type="submit" name="action_type" value="export" class="btn btn-primary waves-effect waves-light">
                    Export Excel
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-2">


                    @php
                        use Illuminate\Support\Carbon;

                        $tanggal = request('DATE_REPORT') ?? Carbon::today()->format('Y-m-d');
                        $monthly = collect($data['monthlyActivity']);
                        $weekly = collect($data['weeklyActivity']);

                        $teams = $monthly->pluck('TEAM')->merge($weekly->pluck('TEAM'))->unique()->values();

                        $grouped = collect();
                        foreach ($teams as $team) {
                            $activities = $monthly->where('TEAM', $team)->values();
                            $plans = $weekly->where('TEAM', $team)->values();

                            $max = max($activities->count(), $plans->count());

                            for ($i = 0; $i < $max; $i++) {
                                $grouped->push([
                                    'TEAM' => $team,
                                    'ACTIVITY' => $activities[$i]->ACTIVITY ?? null,
                                    'PIC_ACTIVITY' => $activities[$i]->PIC ?? null,
                                    'DATE_REPORT' => $activities[$i]->DATE_REPORT ?? null,
                                    'PLAN' => $plans[$i]->WORK_ITEMS ?? null,
                                    'PIC_PLAN' => $plans[$i]->ACTION_BY ?? null,
                                ]);
                            }
                        }

                        $groupedByTeam = $grouped->groupBy('TEAM');
                        $no = 1;

                        $tanggalSekarang = Carbon::parse($tanggal);

                        // Hitung jarak dari Kamis
                        $selisihHari = ($tanggalSekarang->dayOfWeek - 3 + 7) % 7;

                        // Kamis minggu ini
                        $startMingguIni = $tanggalSekarang->copy()->subDays($selisihHari);
                        $endMingguIni = $startMingguIni->copy()->addDays(6);

                        // Kamis dan Rabu minggu depan
                        $startMingguDepan = $startMingguIni->copy()->addDays(7);
                        $endMingguDepan = $endMingguIni->copy()->addDays(7);
                    @endphp
                    <h4>Tanggal Laporan: {{ \Carbon\Carbon::parse($startMingguIni)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($endMingguDepan)->translatedFormat('d F Y') }}</h4>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover nowrap w-100">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" style="text-align: center">No</th>
                                    <th rowspan="2" style="text-align: center">Team</th>
                                    <th colspan="2" style="text-align: center">Activity ({{ \Carbon\Carbon::parse($startMingguIni)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endMingguIni)->translatedFormat('d F Y') }})</th>
                                    <th colspan="2" style="text-align: center">Plan ({{ \Carbon\Carbon::parse($startMingguDepan)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endMingguDepan)->translatedFormat('d F Y') }})</th>
                                </tr>
                                <tr>
                                    <th>Work Items</th>
                                    <th>PIC</th>
                                    <th>Work Items</th>
                                    <th>PIC</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($groupedByTeam as $team => $rows)
                                    <tr class="table-secondary">
                                        <td colspan="7"><strong>{{ $team }}</strong></td>
                                    </tr>
                                    @foreach ($rows as $row)
                                        @php
                                            $tanggalFormat = $row['DATE_REPORT']
                                                ? \Carbon\Carbon::parse($row['DATE_REPORT'])->locale('id')->translatedFormat('d F Y')
                                                : null;
                                        @endphp
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $row['TEAM'] }}</td>
                                            <td>
                                                {{ $row['ACTIVITY'] ? "($tanggalFormat) " . $row['ACTIVITY'] : '-' }}
                                            </td>
                                            <td>{{ $row['PIC_ACTIVITY'] ?? '-' }}</td>
                                            <td>{{ $row['PLAN'] ?? '-' }}</td>
                                            <td>{{ $row['PIC_PLAN'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateInput = document.getElementById('basic-datepicker');

        // Ambil parameter dari URL
        const params = new URLSearchParams(window.location.search);
        const dateReport = params.get('DATE_REPORT');

        if (dateReport) {
            dateInput.value = dateReport;
        } else {
            // Buat tanggal sekarang dalam zona waktu Asia/Makassar
            const now = new Date();

            // Format sebagai YYYY-MM-DD di Asia/Makassar
            const makassarDate = new Intl.DateTimeFormat('en-CA', {
                timeZone: 'Asia/Makassar',
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            }).format(now);

            dateInput.value = makassarDate; // Output format: YYYY-MM-DD
        }
    });
</script>

@include('layout.footer')
