@include('layout.head', ['title' => 'Daily Activity'])
@include('layout.sidebar')
@include('layout.header')
<div class="page-container">

    <div class="page-title-box">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="flex-grow-1">
                <h4 class="font-18 mb-0">Daily Activity</h4>
            </div>

            <form action="" method="GET" class="d-flex align-items-center gap-2">
                <input type="text" id="basic-datepicker" class="form-control" name="DATE_REPORT" required
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
                        $grouped = collect($data['dailyActivity'])->groupBy('TEAM');
                        $no = 1;
                    @endphp

                    <h4>Tanggal Laporan: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</h4>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover nowrap w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Team</th>
                                    <th>Jam Action</th>
                                    <th>Jam Finish</th>
                                    <th>Activity</th>
                                    <th>PIC</th>
                                    <th>Reporting</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($grouped as $team => $activities)
                                    <tr class="table-secondary">
                                        <td colspan="7"><strong>{{ $team }}</strong></td>
                                    </tr>
                                    @foreach ($activities as $daily)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $daily->TEAM }}</td>
                                            <td>{{ \Carbon\Carbon::parse($daily->START)->format('H:i') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($daily->FINISH)->format('H:i') }}</td>
                                            <td>{{ $daily->ACTIVITY }}</td>
                                            <td>{{ $daily->PIC }}</td>
                                            <td>{{ $daily->REPORTING }}</td>
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
