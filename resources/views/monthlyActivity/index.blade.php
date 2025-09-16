@include('layout.head', ['title' => 'Monthly Summary'])
@include('layout.sidebar')
@include('layout.header')
<div class="page-container">

    <div class="page-title-box">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="flex-grow-1">
                @php
                    use Illuminate\Support\Carbon;
                    $tanggal = request('DATE_REPORT') ?? Carbon::today()->format('Y-m');
                    @endphp
                <h4 class="font-18 mb-0">Monthly Summary {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('F Y') }}</h4>
            </div>

            <form action="" method="GET" class="d-flex align-items-center gap-2">
                <input type="month" class="form-control" name="DATE_REPORT"
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
                    <h6>Tower</h6>
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered table-hover table-striped align-middle small">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width: 40px;">No</th>
                                    <th style="width: 120px;">Tanggal</th>
                                    <th style="width: 120px;">Tower</th>
                                    <th style="width: 220px;">Deskripsi Problem</th>
                                    <th style="width: 200px;">Action</th>
                                    <th style="width: 220px;">Remarks</th>
                                    <th style="width: 160px;">On-site</th>
                                    <th style="width: 120px;">Reporting</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($towerMonthlyActivity as $tower)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($tower->DATE_REPORT)->format('d-m-Y') }}</td>
                                        <td>{{ $tower->NAMA_ITEM }}</td>
                                        <td class="text-wrap">{{ $tower->ACTUAL_PROBLEM }}</td>
                                        <td class="text-wrap">{{ $tower->ACTION_PROBLEM }}</td>
                                        <td class="text-wrap">{{ $tower->REMARKS }}</td>
                                        <td class="text-wrap">{{ $tower->PIC }}</td>
                                        <td>{{ $tower->REPORTING }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-2">
                    <h6>Unit</h6>
                    <div class="table-responsive">
                        <table id="datatable2" class="table table-bordered table-hover table-striped align-middle small">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width: 40px;">No</th>
                                    <th style="width: 120px;">Tanggal</th>
                                    <th style="width: 120px;">Unit</th>
                                    <th style="width: 220px;">Deskripsi Problem</th>
                                    <th style="width: 200px;">Action</th>
                                    <th style="width: 220px;">Remarks</th>
                                    <th style="width: 160px;">On-site</th>
                                    <th style="width: 120px;">Reporting</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($unitMonthlyActivity as $unit)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($unit->DATE_REPORT)->format('d-m-Y') }}</td>
                                        <td>{{ $unit->NAMA_ITEM }}</td>
                                        <td class="text-wrap">{{ $unit->ACTUAL_PROBLEM }}</td>
                                        <td class="text-wrap">{{ $unit->ACTION_PROBLEM }}</td>
                                        <td class="text-wrap">{{ $unit->REMARKS }}</td>
                                        <td class="text-wrap">{{ $unit->PIC }}</td>
                                        <td>{{ $unit->REPORTING }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@include('layout.footer')
<script>
    $(document).ready(function () {
        if ($.fn.DataTable.isDataTable('#datatable')) {
            $('#datatable').DataTable().destroy();
        }

        $('#datatable').DataTable({
            pageLength: 200,
            lengthMenu: [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
            responsive: false,
            autoWidth: false
        });
    });

    $(document).ready(function () {
        if ($.fn.DataTable.isDataTable('#datatable2')) {
            $('#datatable2').DataTable().destroy();
        }

        $('#datatable2').DataTable({
            pageLength: 200,
            lengthMenu: [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
            responsive: false,
            autoWidth: false
        });
    });
</script>
