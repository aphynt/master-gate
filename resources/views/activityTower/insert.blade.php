@include('layout.head', ['title' => 'Reporting Activity Tower'])
@include('layout.sidebar')
@include('layout.header')
@php use Illuminate\Support\Str; @endphp
<style>
    th,
    td {
        font-size: 11px;
        padding: 4px;
    }

    textarea.form-control,
    select.form-control,
    input.form-control {
        font-size: 11px;
        padding: 4px;
    }

    .select2-container--default .select2-selection--single {
        height: 30px;
        font-size: 11px;
    }

    .select2-container--default .select2-selection--multiple {
        font-size: 11px;
    }

    table.table-fixed {
        table-layout: fixed;
        width: 100%;
    }

    td textarea,
    td select,
    td input {
        max-width: 200px;
        width: 100%;
    }

</style>
<div class="page-container">
    <form action="{{ route('activityTower.post') }}" method="post">
        @csrf
        <div class="page-title-box">

            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-2">
                <div class="flex-grow-1">
                    <h4 class="font-18 mb-0">Reporting Activity Tower</h4>
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
                                <div class="table-responsive">
                                    @csrf
                                    <table class="table table-striped table-fixed">
                                        <thead>
                                            <tr>
                                                <th style="width: 100px;">TOWER</th>
                                                <th style="width: 120px;">ACTIVITY</th>
                                                <th style="width: 200px;">DESCRIPTION PROBLEM</th>
                                                <th style="width: 200px;">ACTION PROBLEM</th>
                                                <th style="width: 80px;">START</th>
                                                <th style="width: 80px;">FINISH</th>
                                                <th style="width: 120px;">STATUS</th>
                                                <th style="width: 180px;">ACTION BY</th>
                                                <th style="width: 180px;">PART & CONSUMABLE</th>
                                                <th style="width: 180px;">REMARKS</th>
                                                <th style="width: 60px;">AKSI</th>
                                            </tr>
                                        </thead>
                                        <tbody id="activity-table">
                                            <tr>
                                                <input type="hidden" name="data[0][UUID]" value="{{ (string) Str::uuid() }}">
                                                <td>
                                                    <select class="form-control tower-select" data-toggle="select2"
                                                        name="data[0][UUID_TOWER]" required>
                                                        @foreach ($tower as $twr)
                                                        <option value="{{ $twr->UUID }}">{{ $twr->NAMA }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control" data-toggle="select2"
                                                        name="data[0][UUID_ACTIVITY]" required>
                                                        @foreach ($activity as $act)
                                                        <option value="{{ $act->UUID }}">{{ $act->KETERANGAN }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <textarea class="form-control"
                                                        name="data[0][ACTUAL_PROBLEM]"></textarea>
                                                </td>
                                                <td>
                                                    <textarea class="form-control"
                                                        name="data[0][ACTION_PROBLEM]"></textarea>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control clockpicker" value="09:30"
                                                        name="data[0][START]" required>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control clockpicker" value="13:14"
                                                        name="data[0][FINISH]" required>
                                                </td>
                                                <td>
                                                    <select class="form-control" data-toggle="select2"
                                                        name="data[0][STATUS]" required>
                                                        @foreach ($status as $sta)
                                                        <option value="{{ $sta->UUID }}">{{ $sta->KETERANGAN }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control select2-multiple" data-toggle="select2"
                                                        multiple name="data[0][ACTION_BY][]">
                                                        @foreach ($user as $us)
                                                        <option value="{{ $us->NRP }}">{{ $us->NAMA_PANGGILAN }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-purple btn-sm"
                                                        data-bs-toggle="modal" data-bs-target="#consumableModal"
                                                        onclick="openConsumableModal(this)">
                                                        Tambah Consumable
                                                    </button>
                                                </td>
                                                <td>
                                                    <textarea class="form-control" name="data[0][REMARKS]"></textarea>
                                                </td>
                                                <td>
                                                    <button class="btn btn-danger btn-sm remove-row">
                                                        <i class="mdi mdi-close"></i>
                                                    </button>
                                                </td>
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

        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <div class="responsive-table-plugin">
                            <div class="table-rep-plugin">
                                <h6>Barang yang digunakan</h6>
                                <table class="table table-sm" id="mainConsumableTable">
                                    <thead>
                                        <tr>
                                            <th>Tower</th>
                                            <th>Barang</th>
                                            <th>Jumlah</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>

                                <input type="hidden" name="CONSUMABLE" id="consumableData">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="consumableModal" tabindex="-1" aria-labelledby="consumableModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="consumableModalLabel">Tambah Consumable</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="consumableSelect" class="form-label">Pilih Barang</label>
                    <select class="form-control" id="consumableSelect" data-toggle="select2">
                        @foreach ($barang as $br)
                            <option value="{{ $br->UUID }}">{{ $br->ITEM }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="consumableQty" class="form-label">Jumlah</label>
                    <input type="number" class="form-control" id="consumableQty" min="1" value="1">
                </div>
                <div class="mb-3">
                    <button type="button" class="btn btn-primary" onclick="addToModalList()">Tambah ke Daftar</button>
                </div>
                <table class="table table-sm" id="modalConsumableTable">
                    <thead>
                        <tr>
                            <th>Tower</th>
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="applyModalConsumables()">Terapkan ke Baris</button>
            </div>
        </div>
    </div>
</div>




@include('layout.footer')

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

        function generateUUIDv4() {
        return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
            (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
        );
    }

        addRowBtn.addEventListener('click', function (e) {
            e.preventDefault();


            const newRow = document.createElement('tr');
            const uuid = generateUUIDv4();
            newRow.innerHTML = `
                <td>
                    <input type="hidden" name="data[${rowIndex}][UUID]" value="${uuid}">
                                                    <select class="form-control tower-select" data-toggle="select2"
                                                        name="data[${rowIndex}][UUID_TOWER]" required>
                                                        @foreach ($tower as $twr)
                                                        <option value="{{ $twr->UUID }}">{{ $twr->NAMA }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control" data-toggle="select2"
                                                        name="data[${rowIndex}][UUID_ACTIVITY]" required>
                                                        @foreach ($activity as $act)
                                                        <option value="{{ $act->UUID }}">{{ $act->KETERANGAN }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <textarea class="form-control"
                                                        name="data[${rowIndex}][ACTUAL_PROBLEM]"></textarea>
                                                </td>
                                                <td>
                                                    <textarea class="form-control"
                                                        name="data[${rowIndex}][ACTION_PROBLEM]"></textarea>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control clockpicker" value="09:30"
                                                        name="data[${rowIndex}][START]" required>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control clockpicker" value="13:14"
                                                        name="data[${rowIndex}][FINISH]" required>
                                                </td>
                                                <td>
                                                    <select class="form-control" data-toggle="select2"
                                                        name="data[${rowIndex}][STATUS]" required>
                                                        @foreach ($status as $sta)
                                                        <option value="{{ $sta->UUID }}">{{ $sta->KETERANGAN }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control select2-multiple" data-toggle="select2"
                                                        multiple name="data[${rowIndex}][ACTION_BY][]">
                                                        @foreach ($user as $us)
                                                        <option value="{{ $us->NRP }}">{{ $us->NAMA_PANGGILAN }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-purple btn-sm"
                                                        data-bs-toggle="modal" data-bs-target="#consumableModal"
                                                        onclick="openConsumableModal(this)">
                                                        Tambah Consumable
                                                    </button>
                                                </td>
                                                <td>
                                                    <textarea class="form-control" name="data[${rowIndex}][REMARKS]"></textarea>
                                                </td>
                                                <td>
                                                    <button class="btn btn-danger btn-sm remove-row">
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
                const rows = document.querySelectorAll('#activity-table tr');

                if (rows.length <= 1) {
                    Swal.fire('Tidak bisa dihapus', 'Minimal harus ada 1 baris.', 'warning');
                    return;
                }



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

    let modalConsumables = [];
    let mainConsumables = [];
    let currentRowIndex = -1;
    let selectedTowerName = '';
    let selectedTowerUUID = '';

    window.addToModalList = function() {
        const consumableSelect = document.getElementById('consumableSelect');
        const qty = document.getElementById('consumableQty').value;
        const uuid = consumableSelect.value;
        const item = consumableSelect.options[consumableSelect.selectedIndex]?.text;

        if (!uuid || qty <= 0) {
            alert('Barang dan jumlah wajib diisi.');
            return;
        }

        modalConsumables.push({
            uuid,
            item,
            qty,
            tower: selectedTowerName,
            towerUUID: selectedTowerUUID
        });

        renderModalConsumables();

        consumableSelect.selectedIndex = 0;
        document.getElementById('consumableQty').value = '1';
    };

    window.renderModalConsumables = function () {
        const tbody = document.querySelector('#modalConsumableTable tbody');


        tbody.innerHTML = '';

        modalConsumables.forEach((c, i) => {
            tbody.innerHTML += `
                <tr>
                    <td>${c.tower}</td>
                    <td>${c.item}</td>
                    <td>${c.qty}</td>
                    <td><button class="btn btn-danger btn-sm" onclick="removeFromModal(${i})">Hapus</button></td>
                </tr>
            `;
        });
    };

    window.removeFromModal = function (index) {
        modalConsumables.splice(index, 1);
        renderModalConsumables();
    };

    window.applyModalConsumables = function () {

        const tableRow = document.querySelector(`#activity-table tr:nth-child(${currentRowIndex + 1})`);
        const consumableCell = tableRow.querySelector('td:nth-child(10)');

        mainConsumables = mainConsumables.concat(modalConsumables);

        renderMainConsumables();

        modalConsumables = [];
        renderModalConsumables();

        const modal = bootstrap.Modal.getInstance(document.getElementById('consumableModal'));
        modal.hide();
    };

    window.renderMainConsumables = function () {
        const tbody = document.querySelector('#mainConsumableTable tbody');
        if (!tbody) return;
        tbody.innerHTML = '';

        mainConsumables.forEach((c, i) => {
            tbody.innerHTML += `
                <tr>
                    <td>${c.tower}</td>
                    <td>${c.item}</td>
                    <td>${c.qty}</td>
                    <td><button class="btn btn-danger btn-sm" onclick="removeFromMain(${i})">Hapus</button></td>
                </tr>
            `;
        });

        document.getElementById('consumableData').value = JSON.stringify(mainConsumables);
    };

    window.removeFromMain = function (index) {
        mainConsumables.splice(index, 1);
        renderMainConsumables();
    };

    window.openConsumableModal = function(button) {
        const row = button.closest('tr');
        const towerSelect = row.querySelector('.tower-select');
        currentRowIndex = [...row.parentNode.children].indexOf(row);
        selectedTowerName = towerSelect.options[towerSelect.selectedIndex].text;
        selectedTowerUUID = towerSelect.value;

        modalConsumables = [];
        renderModalConsumables();

        document.getElementById('consumableModalLabel').innerText = `Tambah Consumable untuk ${selectedTowerName}`;
    }

    $('#consumableModal').on('shown.bs.modal', function () {
        $('#consumableSelect').select2({
            dropdownParent: $('#consumableModal')
        });
    });


</script>
