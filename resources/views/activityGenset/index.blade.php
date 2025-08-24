@include('layout.head', ['title' => 'Activity Genset'])
@include('layout.sidebar')
@include('layout.header')
<div class="page-container">

    <div class="page-title-box">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="flex-grow-1">
                <h4 class="font-18 mb-0">Activity Genset</h4>
            </div>

            <form action="" method="GET" class="d-flex align-items-center gap-2">
                <input type="text" id="basic-datepicker" class="form-control" name="DATE_REPORT" required
                    style="width: 160px;">
                <button type="submit" class="btn btn-outline-primary">Show Report</button>
            </form>

            <div>
                <a href="{{ route('activityGenset.insert') }}" class="btn btn-primary waves-effect waves-light" >Insert</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-2">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th data-priority="1">DATE REPORT</th>
                                <th data-priority="1">NAMA TOWER</th>
                                <th data-priority="1">NO GENSET</th>
                                <th data-priority="1">KEGIATAN</th>
                                <th data-priority="1">START</th>
                                <th data-priority="1">FINISH</th>
                                <th data-priority="1">FUEL</th>
                                <th data-priority="1">REPORTING</th>
                                <th data-priority="6">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activity as $act)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $act->DATE_REPORT }}</td>
                                <td>{{ $act->NAMA_TOWER }}</td>
                                <td>{{ $act->NO_GENSET }}</td>
                                <td>{{ $act->KEGIATAN }}</td>
                                <td>{{ \Carbon\Carbon::parse($act->START)->format('H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($act->FINISH)->format('H:i') }}</td>
                                <td>
                                    @if ($act->FUEL != null)
                                        {{ $act->FUEL }}%
                                    @endif
                                </td>
                                <td>{{ $act->REPORTING }}</td>
                                <td>
                                    @if (Auth::user()->nrp == $act->NRP_REPORTING)
                                        <a href="{{ route('activityGenset.edit', $act->UUID) }}" class="btn btn-purple btn-sm waves-effect waves-light" >Edit</a>

                                        <a href="#deleteGenset{{ $act->UUID }}"
                                        class="btn btn-danger waves-effect waves-light btn-sm" data-animation="contentscale"
                                        data-plugin="custommodal" data-overlaySpeed="100"
                                        data-overlayColor="#36404a">Hapus</a>
                                        @include('activityGenset.modal.delete')
                                    @endif


                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-body pt-2">
                    <h6>Plan Genset <small class="text-muted">({{ \Carbon\Carbon::now()->translatedFormat('d F Y') }})</small></h6>
                    <table id="datatable" class="table table-bordered dt-responsive"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>TOWER</th>
                                <th>ACTIVITY</th>
                                <th>LAST ACTION</th>
                                <th>NEXT ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwals as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $item['tower'] }}</td>
                                <td>{{ $item['last_activity'] }}</td>
                                <td>{{ $item['last_date'] }}</td>
                                <td>{{ $item['next_activity'] }} ({{ $item['next_date'] }})</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateInput = document.getElementById('basic-datepicker');
        console.log(dateInput.value);


        // Ambil parameter dari URL
        const params = new URLSearchParams(window.location.search);
        const dateReport = params.get('DATE_REPORT');
        dateInput.value = dateReport;

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
