@include('layout.head', ['title' => 'Activity Additional'])
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
                <h4 class="font-18 mb-0">Activity Additional</h4>
            </div>

            <form action="" method="GET" class="d-flex align-items-center gap-2">
                <input type="text" id="basic-datepicker" class="form-control" name="DATE_REPORT" required
                    style="width: 160px;">
                <button type="submit" class="btn btn-outline-primary">Show Report</button>
            </form>

            <div>
                <a href="{{ route('activityAdditional.insert') }}" class="btn btn-primary waves-effect waves-light">
                    Insert
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-2">
                    <table id="datatable" class="table table-bordered dt-responsive"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th data-priority="1">DATE REPORT</th>
                                <th data-priority="1">START</th>
                                <th data-priority="1">FINISH</th>
                                <th data-priority="1">TEAM</th>
                                <th data-priority="1">ACTIVITY</th>
                                <th data-priority="1">PERSONIL</th>
                                <th data-priority="1">REPORTING</th>
                                <th data-priority="6">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activity as $act)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $act->DATE_REPORT }}</td>
                                <td>{{ \Carbon\Carbon::parse($act->START)->format('H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($act->FINISH)->format('H:i') }}</td>
                                <td>{{ $act->NAMA_TEAM }}</td>
                                <td>{{ $act->ACTION_PROBLEM }}</td>
                                <td>{{ $act->ACTION_BY }}</td>
                                <td>{{ $act->REPORTING }}</td>
                                <td>
                                    @if (Auth::user()->nrp == $act->NRP_REPORTING)

                                        <div class="d-flex gap-1">
                                            <a href="#editlistActivity{{ $act->UUID }}"
                                            class="btn btn-purple waves-effect waves-light btn-sm" data-animation="contentscale"
                                            data-plugin="custommodal" data-overlaySpeed="100"
                                            data-overlayColor="#36404a">Edit</a>
                                            @include('activityAdditional.modal.edit')

                                            <a href="#deletelistActivity{{ $act->UUID }}"
                                            class="btn btn-danger waves-effect waves-light btn-sm" data-animation="contentscale"
                                            data-plugin="custommodal" data-overlaySpeed="100"
                                            data-overlayColor="#36404a">Hapus</a>
                                            @include('activityAdditional.modal.delete')

                                            <a href="#editPersonil{{ $act->UUID }}"
                                                class="btn btn-warning btn-sm waves-effect waves-light"
                                                data-animation="blur" data-plugin="custommodal">
                                                Edit Personil
                                            </a>
                                            @include('activityAdditional.modal.editPersonil')
                                        </div>
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
