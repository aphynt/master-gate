@include('layout.head', ['title' => 'Insert Weekly Plan'])
@include('layout.sidebar')
@include('layout.header')
<div class="page-container">
    <form action="{{ route('weeklyPlan.post') }}" method="post">
        <div class="page-title-box">

            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-2">
                <div class="flex-grow-1">
                    <h4 class="font-18 mb-0">Insert Weekly Plan</h4>
                </div>
                <div class="text-end">
                    <a href="#" id="add-row-btn" class="btn btn-primary waves-effect waves-light">Tambah Row</a>
                </div>
            </div>



        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="responsive-table-plugin">
                            <div class="table-rep-plugin">
                                <div class="table-responsive" data-pattern="priority-columns">

                                        @csrf
                                        <table id="tech-companies-1" class="table table-striped"
                                            style="table-layout: fixed; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th style="width: 200px; white-space: nowrap;">TEAM</th>
                                                    <th style="width: 130px; white-space: nowrap;">START DATE</th>
                                                    <th style="width: 130px; white-space: nowrap;">END DATE</th>
                                                    <th style="min-width: 300px; white-space: nowrap;">WORK ITEMS</th>
                                                    <th style="width: 300px; white-space: nowrap;">PERSONIL</th>
                                                    <th style="width: 100px; white-space: nowrap;">AKSI</th>
                                                </tr>
                                            </thead>
                                            <tbody id="activity-table">
                                                <tr>
                                                    <td>
                                                        <select class="form-control" data-toggle="select2" name="data[0][TEAM]" required>
                                                            @foreach ($team as $te)
                                                            <option value="{{ $te->UUID }}">{{ $te->NAMA }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input class="form-control startDate" type="text" id="data[0][STARTDATE]" name="data[0][STARTDATE]" required>
                                                    </td>
                                                    <td>
                                                        <input class="form-control endDate" type="text" id="data[0][ENDDATE]" name="data[0][ENDDATE]" required>
                                                    </td>
                                                    <td><textarea class="form-control form-control-sm" style="min-width: 300px;" name="data[0][WORK_ITEMS]" ></textarea>
                                                    </td>
                                                    <td>
                                                        <select class="form-control select2-multiple" data-toggle="select2" multiple="multiple" name="data[0][ACTION_BY][]" data-placeholder="Choose ...">
                                                            @foreach ($user as $us)
                                                            <option value="{{ $us->NRP }}">{{ $us->NAMA_PANGGILAN }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><button class="btn btn-icon waves-effect waves-light btn-danger remove-row"> <i class="mdi mdi-close"></i> </button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="text-end mt-3">
                                            <button type="submit" class="btn btn-success">Posting</button>
                                        </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateInputs = [];
        dateInputs.push(`basic-datepicker`);

        for (let i = 0; i <= 50; i++) {
            dateInputs.push(`data[${i}][STARTDATE]`);
            dateInputs.push(`data[${i}][ENDDATE]`);
        }

        dateInputs.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                const makassarDate = new Intl.DateTimeFormat('en-CA', {
                    timeZone: 'Asia/Makassar',
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit'
                }).format(new Date());

                input.value = makassarDate;
            }
        });

        const addRowBtn = document.getElementById('add-row-btn');
        const tableBody = document.getElementById('activity-table');
        let rowIndex = 1;

        addRowBtn.addEventListener('click', function (e) {
            e.preventDefault();

            const newRow = document.createElement('tr');
            newRow.innerHTML = `
            <tr>
                <td>
                    <select class="form-control" data-toggle="select2" name="data[${rowIndex}][TEAM]" required>
                        @foreach ($team as $te)
                        <option value="{{ $te->UUID }}">{{ $te->NAMA }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input class="form-control startDate" type="text" id="data[${rowIndex}][STARTDATE]" name="data[${rowIndex}][STARTDATE]" required>
                </td>
                <td>
                    <input class="form-control endDate" type="text" id="data[${rowIndex}][ENDDATE]" name="data[${rowIndex}][ENDDATE]" required>
                </td>
                <td><textarea class="form-control form-control-sm" style="min-width: 300px;" name="data[${rowIndex}][WORK_ITEMS]" ></textarea>
                </td>
                <td>
                    <select class="form-control select2-multiple" data-toggle="select2" multiple="multiple" name="data[${rowIndex}][ACTION_BY][]" data-placeholder="Choose ...">
                        @foreach ($user as $us)
                        <option value="{{ $us->NRP }}">{{ $us->NAMA_PANGGILAN }}
                        </option>
                        @endforeach
                    </select>
                </td>
                <td><button class="btn btn-icon waves-effect waves-light btn-danger remove-row"> <i class="mdi mdi-close"></i> </button></td>
            </tr>

            `;

            const lastSelect = tableBody.querySelectorAll('.select2-multiple');
            const lastSelectstartDate = tableBody.querySelectorAll('.startDate');
            const lastSelectendDate = tableBody.querySelectorAll('.endDate');

            let lastStartDateValue = '';
            let lastEndDateValue = '';
            let selectedValues = null;

            // Ambil value dari input terakhir .startDate dan .endDate
            if (lastSelectstartDate.length > 0) {
                lastStartDateValue = lastSelectstartDate[lastSelectstartDate.length - 1].value;
            }

            if (lastSelectendDate.length > 0) {
                lastEndDateValue = lastSelectendDate[lastSelectendDate.length - 1].value;
            }

            // Ambil value select2 terakhir
            if (lastSelect.length > 0) {
                const last = lastSelect[lastSelect.length - 1];
                selectedValues = $(last).val();
            }

            // Append baris baru ke table
            tableBody.appendChild(newRow);

            // Inisialisasi select2 dan clockpicker setelah newRow ditambahkan
            if (typeof $ !== 'undefined') {
                $(newRow).find('[data-toggle="select2"]').select2();
                $(newRow).find('.select2-multiple').select2();
                $(newRow).find('.clockpicker').clockpicker();
            }

            // Isi value select2 jika ada
            if (selectedValues) {
                const newSelect = newRow.querySelector('.select2-multiple');
                $(newSelect).val(selectedValues).trigger('change');
            }

            // Isi value startDate dan endDate jika ada
            const newStartDate = newRow.querySelector('.startDate');
            const newEndDate = newRow.querySelector('.endDate');

            if (newStartDate) newStartDate.value = lastStartDateValue;
            if (newEndDate) newEndDate.value = lastEndDateValue;


            if (typeof $ !== 'undefined') {
                $('[data-toggle="select2"]').select2();
                $('.select2-multiple').select2();
                $('.clockpicker').clockpicker();
            }

            rowIndex++;
        });

        tableBody.addEventListener('click', function (e) {
            if (e.target.closest('.remove-row')) {

                e.preventDefault();
                const row = e.target.closest('tr');


                Swal.fire({
                    title: 'Hapus baris ini?',
                    text: "Data tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        row.remove();
                        Swal.fire('Dihapus!', 'Baris berhasil dihapus.', 'success');
                    }
                });
            }
        });
    });
</script>


@include('layout.footer')
