@include('layout.head', ['title' => 'History Pergantian Barang'])
@include('layout.sidebar')
@include('layout.header')
<div class="page-container">

    <div class="page-title-box">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="flex-grow-1">
                <h4 class="font-18 mb-0">History Pergantian Barang</h4>
            </div>

            <form action="" method="GET" class="d-flex align-items-center gap-2">
                <input type="text" id="basic-datepicker" class="form-control" name="DATE_REPORT" required
                    style="width: 160px;">
                <button type="submit" class="btn btn-outline-primary">Show Report</button>
            </form>

            <div>
                <a href="{{ route('activityPergantianBarang.insert') }}" class="btn btn-primary waves-effect waves-light">
                    Insert Data
                </a>
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
                                <th data-priority="1">TANGGAL_PELEPASAN</th>
                                <th data-priority="1">PERANGKAT DILEPAS</th>
                                <th data-priority="1">SN BARANG DILEPAS</th>
                                <th data-priority="1">POSISI BARANG SEKARANG</th>
                                <th data-priority="1">PIC</th>
                                <th data-priority="1">REMARKS</th>
                                <th data-priority="1">REPORTING</th>
                                <th data-priority="6">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activity as $act)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $act->TANGGAL_PELEPASAN }}</td>
                                <td>{{ $act->PERANGKAT_DILEPAS }}</td>
                                <td>{{ $act->SN_BARANG_DILEPAS }}</td>
                                <td>{{ $act->POSISI_BARANG }}</td>
                                <td>{{ $act->ACTION_BY }}</td>
                                <td>{{ $act->REMARKS }}</td>
                                <td>{{ $act->REPORTING }}</td>
                                <td>
                                    @if (Auth::user()->nrp == $act->NRP_REPORTING)
                                        <a href="{{ route('activityPergantianBarang.edit', $act->UUID) }}"
                                        class="btn btn-purple waves-effect waves-light btn-sm" >Edit</a>
                                        {{-- @include('activityPergantianBarang.modal.edit') --}}

                                        <a href="#deletelistActivity{{ $act->UUID }}"
                                        class="btn btn-danger waves-effect waves-light btn-sm" data-animation="contentscale"
                                        data-plugin="custommodal" data-overlaySpeed="100"
                                        data-overlayColor="#36404a">Hapus</a>
                                        @include('activityPergantianBarang.modal.delete')
                                    @endif
                                </td>
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
