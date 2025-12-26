@include('layout.head', ['title' => 'Activity Unit'])
@include('layout.sidebar')
@include('layout.header')
<style>
    #datatable {
        table-layout: fixed;
        width: 100%;
    }

    .wrap-text {
        max-width: 250px;
        white-space: normal;
        word-wrap: break-word;
        word-break: break-word;
    }

</style>
<div class="page-container">

    <div class="page-title-box">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="flex-grow-1">
                <h4 class="font-18 mb-0">Activity Unit</h4>
            </div>

            <form action="" method="GET" class="d-flex align-items-center gap-2">
                <input type="text" id="basic-datepicker" class="form-control" name="DATE_REPORT" required
                    style="width: 160px;">
                <button type="submit" class="btn btn-outline-primary">Show Report</button>
            </form>
            <div>
                <button id="btn-detail-data" class="btn btn-info waves-effect waves-light" type="button">
                    Detail
                </button>
            </div>
            <div>
                <button id="btn-edit-data" class="btn btn-purple waves-effect waves-light" type="button">
                    Edit
                </button>
            </div>
            <div>
                <a href="{{ route('activityUnit.insert') }}" class="btn btn-primary waves-effect waves-light">
                    Insert
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-2">
                    <form id="form-unit" >
                        <table id="datatable" class="table table-bordered dt-responsive"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th data-priority="1">UNIT</th>
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
                                @foreach ($unit as $unt)
                                <tr style="{{ $unt->NAMA_STATUS == 'Open' ? 'background-color: #fff3cd;' : '' }}">
                                    <td>
                                        @if (Auth::user()->nrp == $unt->NRP_REPORTING)
                                        <input type="checkbox" name="selected_items[]" value="{{ $unt->UUID }}" style="cursor: pointer;">
                                        @endif
                                    </td>
                                    <td>{{ $unt->NAMA_UNIT }}</td>
                                    <td>{{ $unt->DATE_ACTION }}</td>
                                    <td>{{ $unt->NAMA_ACTIVITY }}</td>
                                    <td>{{ $unt->ACTUAL_PROBLEM }}</td>
                                    <td>{{ $unt->ACTION_PROBLEM }}</td>
                                    <td>{{ date('H:i', strtotime($unt->START)) }}</td>
                                    <td>{{ date('H:i', strtotime($unt->FINISH)) }}</td>
                                    <td>{{ $unt->NAMA_STATUS }}</td>
                                    <td>{{ $unt->ACTION }}</td>
                                    <td>{{ $unt->REMARKS }}</td>
                                    <td>{{ $unt->REPORTING }}</td>
                                    <td>
                                        @if (Auth::user()->nrp == $unt->NRP_REPORTING)
                                        <div class="d-flex gap-1">
                                            <a href="#deletelistActivity{{ $unt->UUID }}"
                                                class="btn btn-danger btn-sm waves-effect waves-light"
                                                data-animation="contentscale" data-plugin="custommodal">
                                                Hapus
                                            </a>

                                            <a href="#editPersonil{{ $unt->UUID }}"
                                                class="btn btn-warning btn-sm waves-effect waves-light"
                                                data-animation="blur" data-plugin="custommodal">
                                                Edit Personil
                                            </a>
                                        </div>
                                        @include('activityUnit.modal.delete')

                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </form>
                    @foreach ($unit as $unt)
                        @include('activityUnit.modal.editPersonil')
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@include('layout.footer')
<script>
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('add-action')) {
            const uuid = e.target.dataset.uuid;
            const container = document.getElementById('actionByContainer' + uuid);
            const index = container.children.length + 1;

            const row = document.createElement('div');
            row.className = 'mb-3 d-flex align-items-center action-row';
            row.innerHTML = `
                <div class="flex-grow-1 me-2">
                    <label class="form-label">Personil ${index}</label>
                    <select class="form-select" name="action_by[${uuid}][]">
                        <option selected>-- Pilih Personil --</option>
                        @foreach ($user as $uss)
                            <option value="{{ $uss->NRP }}">{{ $uss->NAME }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-action mt-4">
                    Hapus
                </button>
            `;

            container.appendChild(row);
        }

        // ============================
        // HAPUS PERSONIL
        // ============================
        if (e.target.classList.contains('remove-action')) {
            const row = e.target.closest('.action-row');
            const container = row.parentElement;

            row.remove();

            // Re-number label
            Array.from(container.children).forEach((el, idx) => {
                const label = el.querySelector('label');
                if (label) {
                    label.textContent = 'Personil ' + (idx + 1);
                }
            });
        }
    });

</script>
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

document.getElementById('form-unit').addEventListener('submit', function(e) {
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

document.getElementById('btn-detail-data').addEventListener('click', function() {
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

    const url = `{{ url('activityUnit/detail') }}?ids=` + uuids.join(',');

    window.location.href = url;
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

    const url = `{{ url('activityUnit/edit') }}?ids=` + uuids.join(',');

    window.location.href = url;
});

</script>
