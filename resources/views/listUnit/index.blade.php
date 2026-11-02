@include('layout.head', ['title' => 'List Unit'])
@include('layout.sidebar')
@include('layout.header')
<style>
    .text-muted {
    color: #000000 !important;
}
</style>
<div class="page-container">

                <div class="page-title-box">

                    <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-2">
                        <div class="flex-grow-1">
                            <h4 class="font-18 mb-0">List Unit</h4>
                        </div>

                       <form action="" method="GET" class="d-flex align-items-center gap-2">
                            <button type="submit" name="action_type" value="export" class="btn btn-primary waves-effect waves-light">
                                Export Excel
                            </button>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-start border-primary border-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Total Unit</p>
                                        <h3 class="mb-0">{{ number_format($totalUnit) }}</h3>
                                    </div>
                                    <div class="avatar-md bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="fas fa-truck text-primary fs-3"></i>
                                    </div>
                                </div>
                                <small class="text-muted">Seluruh unit terdaftar</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-start border-success border-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Converter Terpasang</p>
                                        <h3 class="mb-0 text-success">{{ number_format($totalTerpasang) }}</h3>
                                    </div>
                                    <div class="avatar-md bg-success-subtle rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="fas fa-check-circle text-success fs-3"></i>
                                    </div>
                                </div>
                                <small class="text-muted">{{ $persentaseTerpasang }}% dari total unit</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-start border-warning border-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Belum Terpasang</p>
                                        <h3 class="mb-0 text-warning">{{ number_format($totalBelumTerpasang) }}</h3>
                                    </div>
                                    <div class="avatar-md bg-warning-subtle rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="fas fa-exclamation-circle text-warning fs-3"></i>
                                    </div>
                                </div>
                                <small class="text-muted">Unit tanpa converter</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-start border-info border-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Ada Riwayat Barang Keluar</p>
                                        <h3 class="mb-0 text-info">{{ number_format($totalAdaRiwayat) }}</h3>
                                    </div>
                                    <div class="avatar-md bg-info-subtle rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="fas fa-calendar-check text-info fs-3"></i>
                                    </div>
                                </div>
                                <small class="text-muted">Unit dengan riwayat converter</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="card-title mb-0">Progress Pemasangan Converter 24V DC to DC</h5>
                                    <h5 class="mb-0 text-success">{{ $persentaseTerpasang }}%</h5>
                                </div>
                                <div class="progress" style="height: 18px;">
                                    <div class="progress-bar bg-success"
                                        role="progressbar"
                                        style="width: {{ $persentaseTerpasang }}%;"
                                        aria-valuenow="{{ $persentaseTerpasang }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100">
                                        {{ $persentaseTerpasang }}%
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <small class="text-success">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Terpasang: {{ $totalTerpasang }} Unit
                                    </small>
                                    <small class="text-warning">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        Belum Terpasang: {{ $totalBelumTerpasang }} Unit
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="responsive-table-plugin">
                                    <div class="table-rep-plugin">
                                        <div class="dt-responsive table-responsive" >
                                            <table id="datatable" class="table table-bordered dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th data-priority="1">VEHICLE</th>
                                                        <th data-priority="1">GROUP</th>
                                                        <th data-priority="1">TYPE</th>
                                                        <th data-priority="1">IP ADDRESS</th>
                                                        <th data-priority="1">CONVERTER 24V DC TO DC</th>
                                                        <th data-priority="1">TANGGAL PEMASANGAN</th>
                                                        <th data-priority="1">RIWAYAT CONVERTER</th>
                                                        <th data-priority="1">STATUS ENABLED</th>
                                                        <th data-priority="1">AKSI</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($listUnit as $item)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $item->VHC_ID }}</td>
                                                            <td>{{ $item->GROUP_ID }}</td>
                                                            <td>{{ $item->TYPE_ID }}</td>
                                                            <td>{{ $item->IP_ADDRESS }}</td>
                                                            <td class="text-center">
    @if ($item->STATUS_CONVERTER_OTOMATIS == 1)
        <span class="badge bg-success">
            <i class="fas fa-check-circle me-1"></i> Terpasang
        </span>
    @else
        <span class="badge bg-warning text-dark">
            <i class="fas fa-clock me-1"></i> Belum Terpasang
        </span>
    @endif
</td>
<td class="text-center">
    @if ($item->TANGGAL_PEMASANGAN)
        {{ \Carbon\Carbon::parse($item->TANGGAL_PEMASANGAN)->format('d/m/Y') }}
    @else
        <span class="text-muted">-</span>
    @endif
</td>
<td class="text-center">
    @if ($item->JUMLAH_RIWAYAT > 0)
        <span class="badge bg-info">
            {{ $item->JUMLAH_RIWAYAT }} Riwayat
        </span>
    @else
        <span class="badge bg-secondary">
            Tidak Ada
        </span>
    @endif
</td>
                                                            <td>{{ $item->STATUSENABLED == true ? '✔️' : '❌' }}</td>
                                                            <td>
                                                                <button
                                                                    type="button"
                                                                    class="btn btn-purple btn-sm"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#editModal"

                                                                    data-uuid="{{ $item->UUID }}"
                                                                    data-vhc="{{ $item->VHC_ID }}"
                                                                    data-converter="{{ $item->CONVERTER_DC_TO_DC }}"
                                                                    data-status="{{ $item->STATUSENABLED }}">
                                                                    Edit
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            @include('listUnit.modal.edit')
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
    $('#datatable').DataTable({
    pageLength: 50,
    lengthMenu: [
        [10, 25, 50, 100, -1],
        [10, 25, 50, 100, "All"]
    ]
});

$('#editModal').on('show.bs.modal', function (event) {

    let button = $(event.relatedTarget);

    let uuid = button.data('uuid');
    let vhc = button.data('vhc');
    let status = button.data('status');

    $('#vhc').val(vhc);

    $('input[name="STATUSENABLED"][value="' + status + '"]')
        .prop('checked', true);

    $('#editForm').attr('action', '/listUnit/update/' + uuid);

});
</script>
