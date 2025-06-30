@include('layout.head', ['title' => 'Activity Tower'])
@include('layout.sidebar')
@include('layout.header')

<div class="page-container">

    <div class="page-title-box">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="flex-grow-1">
                <h4 class="font-18 mb-0">Activity Tower</h4>
            </div>

            <form action="" method="GET" class="d-flex align-items-center gap-2">
                <input type="text" id="basic-datepicker" class="form-control" name="DATE_REPORT" required
                    style="width: 160px;">
                <button type="submit" class="btn btn-outline-primary">Show Report</button>
            </form>

            <div>
                <button id="btn-edit-data" class="btn btn-purple waves-effect waves-light" type="button">
                    Edit Data
                </button>
            </div>
            <div>
                <a href="{{ route('activityTower.insert') }}" class="btn btn-primary waves-effect waves-light">
                    Insert Data
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-2">
                    <form id="form-tower" >
                        <table id="datatable" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th data-priority="1">TOWER</th>
                                    <th data-priority="1">DATE ACTION</th>
                                    <th data-priority="1">ACTIVITY</th>
                                    <th data-priority="1">DESKRIPSI PROBLEM</th>
                                    <th data-priority="1">ACTION PROBLEM</th>
                                    <th data-priority="1">START</th>
                                    <th data-priority="1">FINISH</th>
                                    <th data-priority="1">STATUS</th>
                                    <th data-priority="1">ACTION BY</th>
                                    <th data-priority="1">REMARKS</th>
                                    <th data-priority="1">REPORTING</th>
                                    <th data-priority="6">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tower as $twr)
                                <tr>
                                    <td>
                                        @if (Auth::user()->nrp == $twr->NRP_REPORTING)
                                        <input type="checkbox" name="selected_items[]" value="{{ $twr->UUID }}" style="cursor: pointer;">
                                        @endif
                                    </td>
                                    <td>{{ $twr->NAMA_TOWER }}</td>
                                    <td>{{ $twr->DATE_ACTION }}</td>
                                    <td>{{ $twr->NAMA_ACTIVITY }}</td>
                                    <td>{{ $twr->ACTUAL_PROBLEM }}</td>
                                    <td>{{ $twr->ACTION_PROBLEM }}</td>
                                    <td>{{ date('H:i', strtotime($twr->START)) }}</td>
                                    <td>{{ date('H:i', strtotime($twr->FINISH)) }}</td>
                                    <td>{{ $twr->NAMA_STATUS }}</td>
                                    <td>{{ $twr->ACTION_BY }}</td>
                                    <td>{{ $twr->REMARKS }}</td>
                                    <td>{{ $twr->REPORTING }}</td>
                                    <td>
                                        @if (Auth::user()->nrp == $twr->NRP_REPORTING)
                                            <a href="#deletelistActivity{{ $twr->UUID }}"
                                            class="btn btn-danger waves-effect waves-light btn-sm" data-animation="contentscale"
                                            data-plugin="custommodal" data-overlaySpeed="100"
                                            data-overlayColor="#36404a">Hapus</a>
                                            @include('activityTower.modal.delete')
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dateInput = document.getElementById('basic-datepicker');
    const params = new URLSearchParams(window.location.search);
    const dateReport = params.get('DATE_REPORT');

    if (dateReport) {
        dateInput.value = dateReport;
    } else {
        const now = new Date();
        const makassarDate = new Intl.DateTimeFormat('en-CA', {
            timeZone: 'Asia/Makassar',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        }).format(now);
        dateInput.value = makassarDate;
    }
});

document.getElementById('form-tower').addEventListener('submit', function(e) {
    e.preventDefault();
});

document.querySelectorAll('input[name="selected_items[]"]').forEach(cb => {
    cb.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});

document.querySelectorAll('#datatable tbody tr').forEach(row => {
    row.addEventListener('click', function(e) {
        if (e.target.type === 'checkbox') return;

        const checkbox = this.querySelector('input[type="checkbox"]');
        if (checkbox) {
            checkbox.checked = !checkbox.checked;
        }
    });
});

document.getElementById('btn-edit-data').addEventListener('click', function() {
    const checkedBoxes = document.querySelectorAll('input[name="selected_items[]"]:checked');

    if (checkedBoxes.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Upps...',
            text: 'Pilih minimal satu data!',
            confirmButtonText: 'OK'
        });
        return;
    }

    const uuids = Array.from(checkedBoxes).map(cb => cb.value);

    const url = `{{ url('activityTower/edit') }}?ids=` + uuids.join(',');

    window.location.href = url;
});
</script>

@include('layout.footer')
