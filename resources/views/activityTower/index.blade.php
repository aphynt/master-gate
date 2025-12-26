@include('layout.head', ['title' => 'Activity Tower'])
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
                <h4 class="font-18 mb-0">Activity Tower</h4>
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
                <a href="{{ route('activityTower.insert') }}" class="btn btn-primary waves-effect waves-light">
                    Insert
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-2">
                    <form id="form-tower">
                        <table id="datatable" class="table table-bordered dt-responsive"
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
                                <tr style="{{ $twr->NAMA_STATUS == 'Open' ? 'background-color: #fff3cd;' : '' }}">
                                    <td>
                                        {{-- @if (Auth::user()->nrp == $twr->NRP_REPORTING) --}}
                                        <input type="checkbox" name="selected_items[]" value="{{ $twr->UUID }}"
                                            style="cursor: pointer;">
                                        {{-- @endif --}}
                                    </td>
                                    <td>{{ $twr->NAMA_TOWER }}</td>
                                    <td>{{ $twr->DATE_ACTION }}</td>
                                    <td>{{ $twr->NAMA_ACTIVITY }}</td>
                                    <td class="wrap-text">{{ $twr->ACTUAL_PROBLEM }}</td>
                                    <td class="wrap-text">{{ $twr->ACTION_PROBLEM }}</td>
                                    <td>{{ date('H:i', strtotime($twr->START)) }}</td>
                                    <td>{{ date('H:i', strtotime($twr->FINISH)) }}</td>
                                    <td>{{ $twr->NAMA_STATUS }}</td>
                                    <td>{{ $twr->ACTION_BY }}</td>
                                    <td>{{ $twr->REMARKS }}</td>
                                    <td>{{ $twr->REPORTING }}</td>
                                    <td>
                                        @if (Auth::user()->nrp == $twr->NRP_REPORTING)
                                        <div class="d-flex gap-1">
                                            <a href="#deletelistActivity{{ $twr->UUID }}"
                                                class="btn btn-danger btn-sm waves-effect waves-light"
                                                data-animation="contentscale" data-plugin="custommodal">
                                                Hapus
                                            </a>

                                            <a href="#editPersonil{{ $twr->UUID }}"
                                                class="btn btn-warning btn-sm waves-effect waves-light"
                                                data-animation="blur" data-plugin="custommodal">
                                                Edit Personil
                                            </a>
                                        </div>
                                        @include('activityTower.modal.delete')
                                        @endif
                                    </td>
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </form>
                    @foreach ($tower as $twr)
                            @include('activityTower.modal.editPersonil')
                        @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

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

    document.getElementById('form-tower').addEventListener('submit', function (e) {
        e.preventDefault();
    });

    document.querySelectorAll('input[name="selected_items[]"]').forEach(cb => {
        cb.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    });

    document.querySelectorAll('#datatable tbody tr').forEach(row => {
        row.addEventListener('click', function (e) {
            if (e.target.type === 'checkbox') return;

            const checkbox = this.querySelector('input[type="checkbox"]');
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
            }
        });
    });

    document.getElementById('btn-detail-data').addEventListener('click', function () {
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

        const url = `{{ url('activityTower/detail') }}?ids=` + uuids.join(',');

        window.location.href = url;
    });
    document.getElementById('btn-edit-data').addEventListener('click', function () {
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
    document.addEventListener('click', function (e) {

        if (!e.target.classList.contains('save-personil')) return;

        const formId = e.target.getAttribute('form');
        const form = document.getElementById(formId);

        if (!form) {
            alert('Form tidak ditemukan');
            e.preventDefault();
            return;
        }

        const uuid = formId.replace('formPersonil-', '');
        const container = document.getElementById('actionByContainer' + uuid);

        if (!container) {
            alert('Data personil tidak ditemukan');
            e.preventDefault();
            return;
        }

        const inputs = container.querySelectorAll('input, select');

        if (inputs.length === 0) {
            alert('Minimal 1 personil harus diisi');
            e.preventDefault();
            return;
        }

        for (const el of inputs) {
            if (!el.value || el.value.trim() === '') {
                alert('Masih ada personil yang kosong');
                e.preventDefault();
                return;
            }
        }
    });

</script>

@include('layout.footer')
