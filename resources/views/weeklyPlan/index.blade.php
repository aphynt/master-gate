@include('layout.head', ['title' => 'Weekly Plan'])
@include('layout.sidebar')
@include('layout.header')
<div class="page-container">

    <div class="page-title-box">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="flex-grow-1">
                <h4 class="font-18 mb-0">Weekly Plan</h4>
            </div>

            <form action="" method="GET" class="d-flex align-items-center gap-2">
                <input type="text" id="basic-datepicker" class="form-control" name="DATE_REPORT" required
                    style="width: 160px;" value="{{ request('DATE_REPORT') }}">

                <button type="submit" class="btn btn-outline-primary">
                    Show Report
                </button>

                <a href="{{ route('weeklyPlan.insert') }}" class="btn btn-primary waves-effect waves-light">Insert
                    Data</a>
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
                    <h4>Tanggal Plan: {{ \Carbon\Carbon::parse($startMingguIni)->translatedFormat('d F Y') }} s/d
                        {{ \Carbon\Carbon::parse($endMingguIni)->translatedFormat('d F Y') }}</h4>

                    <div class="responsive-table-plugin">
                        <div class="table-rep-plugin">
                            <div class="table-responsive" data-pattern="priority-columns">
                                <table id="tech-companies-1" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th data-priority="1">TEAM</th>
                                            <th data-priority="1">START DATE</th>
                                            <th data-priority="1">END DATE</th>
                                            <th data-priority="1">WORK ITEMS</th>
                                            <th data-priority="1">PIC</th>
                                            <th data-priority="1">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($weekly as $we)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $we->TEAM }}</td>
                                            <td>{{ $we->STARTDATE }}</td>
                                            <td>{{ $we->ENDDATE }}</td>
                                            <td>{{ $we->WORK_ITEMS }}</td>
                                            <td>{{ $we->ACTION_BY }}</td>
                                            <td>
                                                @if (Auth::user()->nrp == $we->REPORTING)
                                                    {{-- <a href="{{ route('activityGenset.edit', $we->UUID) }}" class="btn btn-purple btn-sm waves-effect waves-light" >Edit</a> --}}

                                                    <a href="#deleteWeekly{{ $we->UUID }}"
                                                    class="btn btn-danger waves-effect waves-light btn-sm" data-animation="contentscale"
                                                    data-plugin="custommodal" data-overlaySpeed="100"
                                                    data-overlayColor="#36404a">Hapus</a>
                                                    @include('weeklyPlan.modal.delete')
                                                @endif


                                            </td>
                                        </tr>
                                        {{-- @include('listActivity.modal.edit') --}}
                                        @endforeach
                                    </tbody>
                                </table>
                            </div> <!-- end .table-responsive -->

                        </div> <!-- end .table-rep-plugin-->
                    </div> <!-- end .responsive-table-plugin-->
                </div>
            </div> <!-- end card-box -->
        </div> <!-- end col -->
    </div>
    <!-- end row -->
</div>
@include('layout.footer')
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
