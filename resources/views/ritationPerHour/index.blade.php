@include('layout.head', ['title' => 'Ritation Per Hour'])
@include('layout.sidebar')
@include('layout.header')

<div class="page-container">

    <div class="page-title-box">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="flex-grow-1">
                <h4 class="font-18 mb-0">Ritation Per Hour</h4>
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
        </div>
    </div>

    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-body pt-2">
                    <form id="form-tower" >
                        <table class="table table-bordered"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th rowspan="2">Select</th>
                                    <th rowspan="2" style="min-width: 100px;">JAM</th>
                                    <th colspan="4" style="text-align: center;" id="tanggal-hari-ini"></th>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">Total</th>
                                    <th style="text-align: left;">Realtime</th>
                                    <th style="text-align: left;">Ach</th>
                                    <th style="text-align: left; min-width: 500px;">Information</th>
                                </tr>
                            </thead>
                            <tbody id="tower-tbody">
                                @php
                                    $sumRealtime = 0;
                                    $sumTotal = 0;
                                @endphp
                                @foreach ($finalRitation as $final)
                                @php
                                    $sumRealtime += $final['REALTIME'];
                                    $sumTotal += $final['TOTAL'];
                                @endphp
                                    <tr>
                                        <td style="text-align: left"><input type="checkbox" name="selected_items[]" value="{{ $final['CODE'] }}" style="cursor: pointer;"></td>
                                        <td style="text-align: left">{{ $final['RANGEHOUR'] }}</td>
                                        <td style="text-align: left">{{ $final['TOTAL'] }}</td>
                                        <td style="text-align: left">{{ $final['REALTIME'] }}</td>
                                        @php
                                            $ach = ($final['TOTAL'] > 0) ? $final['REALTIME'] / $final['TOTAL'] * 100 : 0;
                                        @endphp
                                        <td style="text-align: left; {{ $ach > 0 && $ach < 95.0 ? 'color: red;' : '' }}">
                                            {{ number_format($ach, 1) }}%
                                        </td>
                                        <td style="text-align: left">{{ $final['INFORMATION'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" style="text-align: right">Total</th>
                                    <th style="text-align: left">
                                        {{ $sumTotal > 0 ? number_format($sumRealtime / $sumTotal * 100, 1) . '%' : '0%' }}
                                    </th>
                                    <th></th>
                                </tr>
                            </tfoot>
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

document.addEventListener('DOMContentLoaded', function () {
    const dateElement = document.getElementById('tanggal-hari-ini');
    const params = new URLSearchParams(window.location.search);
    const dateReport = params.get('DATE_REPORT');

    function formatTanggalIndonesia(dateString) {
        const bulanIndo = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];
        const date = new Date(dateString);
        const day = date.getDate().toString().padStart(2, '0');
        const month = bulanIndo[date.getMonth()];
        const year = date.getFullYear();
        return `${day} ${month} ${year}`;
    }

    if (dateReport) {
        // format dari URL diasumsikan dalam format YYYY-MM-DD
        dateElement.textContent = formatTanggalIndonesia(dateReport);
    } else {
        const now = new Date();
        const makassarDate = new Date(now.toLocaleString("en-US", { timeZone: "Asia/Makassar" }));
        dateElement.textContent = formatTanggalIndonesia(makassarDate);
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

    const code = Array.from(checkedBoxes).map(cb => cb.value);

    const url = `{{ url('ritationPerHour/edit') }}?ids=` + code.join(',');

    window.location.href = url;
});
</script>

@include('layout.footer')
