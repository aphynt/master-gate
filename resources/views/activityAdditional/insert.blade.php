@include('layout.head', ['title' => 'Reporting Activity Additional'])
@include('layout.sidebar')
@include('layout.header')
<div class="page-container">
    <form action="{{ route('activityAdditional.post') }}" method="post">
        <div class="page-title-box">

            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-2">
                <div class="flex-grow-1">
                    <h4 class="font-18 mb-0">Reporting Activity Additional</h4>
                </div>
                <div class="text-end">
                    Date Report:
                </div>
                <div class="text-end">
                    <input type="text" id="basic-datepicker" class="form-control" name="DATE_REPORT" required>
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
                                                    <th style="width: 130px; white-space: nowrap;">START</th>
                                                    <th style="width: 130px; white-space: nowrap;">FINISH</th>
                                                    <th style="min-width: 300px; white-space: nowrap;">ACTIVITY</th>
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
                                                        <div class="input-group clockpicker">
                                                            <input type="text" class="form-control" value="09:30" name="data[0][START]" required>
                                                            <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group clockpicker" data-placement="top"
                                                            data-align="top" data-autoclose="true">
                                                            <input type="text" class="form-control" value="13:14" name="data[0][FINISH]" required>
                                                            <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                                                        </div>
                                                    </td>
                                                    <td><textarea class="form-control form-control-sm" style="min-width: 300px;" name="data[0][ACTION_PROBLEM]" ></textarea>
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
        const dateInput = document.getElementById('basic-datepicker');

        const makassarDate = new Intl.DateTimeFormat('en-CA', {
            timeZone: 'Asia/Makassar',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        }).format(new Date());

        dateInput.value = makassarDate;

        const addRowBtn = document.getElementById('add-row-btn');
        const tableBody = document.getElementById('activity-table');
        let rowIndex = 1;

        addRowBtn.addEventListener('click', function (e) {
            e.preventDefault();

            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>
                    <select class="form-control" name="data[${rowIndex}][TEAM]">
                        @foreach ($team as $te)
                        <option value="{{ $te->UUID }}">{{ $te->NAMA }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <div class="input-group clockpicker">
                        <input type="text" class="form-control" value="09:30" name="data[${rowIndex}][START]">
                        <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                    </div>
                </td>
                <td>
                    <div class="input-group clockpicker">
                        <input type="text" class="form-control" value="13:14" name="data[${rowIndex}][FINISH]">
                        <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                    </div>
                </td>
                <td>
                    <textarea class="form-control form-control-sm" name="data[${rowIndex}][ACTION_PROBLEM]"></textarea>
                </td>
                <td>
                    <select class="form-control select2-multiple" multiple name="data[${rowIndex}][ACTION_BY][]" data-placeholder="Choose ...">
                        @foreach ($user as $us)
                        <option value="{{ $us->NRP }}">{{ $us->NAMA_PANGGILAN }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <button class="btn btn-icon waves-effect waves-light btn-danger remove-row">
                        <i class="mdi mdi-close"></i>
                    </button>
                </td>
            `;

            const lastSelect = tableBody.querySelectorAll('.select2-multiple');
            let selectedValues = null;

            if (lastSelect.length > 0) {
                const last = lastSelect[lastSelect.length - 1];
                selectedValues = $(last).val();
            }


            if (typeof $ !== 'undefined') {
                $('[data-toggle="select2"]').select2();
                $('.select2-multiple').select2();
                $('.clockpicker').clockpicker();
            }

            if (selectedValues) {
                const newSelect = newRow.querySelector('.select2-multiple');
                $(newSelect).val(selectedValues).trigger('change');
            }

            tableBody.appendChild(newRow);


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
