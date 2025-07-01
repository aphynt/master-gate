@include('layout.head', ['title' => 'Maintenance Unit'])
@include('layout.sidebar')
@include('layout.header')
<div class="page-container">

    <div class="page-title-box">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="flex-grow-1">
                <h4 class="font-18 mb-0">Maintenance Unit</h4>
            </div>

           <form action="" method="GET" class="d-flex align-items-center gap-2">
    <input type="month" name="DATE_REPORT" class="form-control" required style="width: 160px;">
    <button type="submit" class="btn btn-outline-primary">Show Report</button>
</form>
            {{-- <div>
                <a href="#" class="btn btn-primary waves-effect waves-light">
                    Export Excel
                </a>
            </div> --}}
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-2">

                    @php
                        use Illuminate\Support\Carbon;

                        $tanggal = request('DATE_REPORT') ?? Carbon::today()->format('Y-m-d');

                        $grouped = collect($activityUnit)->groupBy(function ($item) {
                            return substr($item->NAME, 0, 2);
                        });

                        $no = 1;
                    @endphp

                    <h4>Tanggal Laporan: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('F Y') }}</h4>

                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Remark</th>
                                    <th>Last Maintained</th>
                                    <th>Location</th>
                                    <th>Remarks</th>
                                    <th>Reporting</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($grouped as $code => $units)
                                    {{-- <tr class="table-secondary">
                                        <td colspan="8"><strong>Group: {{ $code }}</strong></td>
                                    </tr> --}}
                                    @foreach ($units as $unit)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $unit->NAME }}</td>
                                            <td>{{ $unit->CODE }}</td>
                                            <td>@if ($unit->STATUS == 'Ready For Maintenance')
                                                    <span class="badge badge-outline-danger">{{ $unit->STATUS }}</span>
                                                @else
                                                    <span class="badge badge-outline-success">{{ $unit->STATUS }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $unit->LAST_MAINTAINED ? \Carbon\Carbon::parse($unit->LAST_MAINTAINED)->translatedFormat('F d, Y') : '' }}</td>
                                            <td>{{ $unit->LOCATION }}</td>
                                            <td>{{ $unit->REMARKS }}</td>
                                            <td>{{ $unit->REPORTING }}</td>
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
</script>
