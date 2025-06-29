@include('layout.head', ['title' => 'Edit Activity Tower'])
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

    /* Agar tabel muat */
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
    <form action="{{ route('activityTower.update') }}" method="post">
        @csrf
        <div class="page-title-box">

            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-2">
                <div class="flex-grow-1">
                    <h4 class="font-18 mb-0">Edit Activity Tower</h4>
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
                                                <th style="width: 100px;">DATE REPORT</th>
                                                <th style="width: 120px;">ACTIVITY</th>
                                                <th style="width: 200px;">DESCRIPTION PROBLEM</th>
                                                <th style="width: 200px;">ACTION PROBLEM</th>
                                                <th style="width: 80px;">START</th>
                                                <th style="width: 80px;">FINISH</th>
                                                <th style="width: 120px;">STATUS</th>
                                                <th style="width: 180px;">ACTION BY</th>
                                                <th style="width: 180px;">PART & CONSUMABLE</th>
                                                <th style="width: 180px;">REMARKS</th>
                                            </tr>
                                        </thead>
                                        <tbody id="activity-table">
                                            @foreach ($dailyTower as $i => $to)
                                            <tr>
                                                <input type="text" class="form-control" name="data[{{ $i }}][UUID]" value="{{ $to->UUID }}" required hidden>
                                                <td>
                                                    <select class="form-control tower-select" data-toggle="select2"
                                                        name="data[{{ $i }}][UUID_TOWER]" required>
                                                        <option value="{{ $to->UUID_TOWER }}">{{ $to->NAMA_TOWER }}</option>
                                                        @foreach ($tower as $twr)
                                                        <option value="{{ $twr->UUID }}">{{ $twr->NAMA }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control basic-datepicker" name="data[{{ $i }}][DATE_REPORT]" value="{{ $to->DATE_REPORT }}" required>
                                                </td>

                                                <td>
                                                    <select class="form-control" data-toggle="select2"
                                                        name="data[{{ $i }}][UUID_ACTIVITY]" required>
                                                        <option value="{{ $to->UUID_ACTIVITY }}">{{ $to->NAMA_ACTIVITY }}</option>
                                                        @foreach ($activity as $act)
                                                        <option value="{{ $act->UUID }}">{{ $act->KETERANGAN }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <textarea class="form-control" name="data[{{ $i }}][ACTUAL_PROBLEM]">{{ $to->ACTUAL_PROBLEM }}</textarea>
                                                </td>
                                                <td>
                                                    <textarea class="form-control" name="data[{{ $i }}][ACTION_PROBLEM]">{{ $to->ACTION_PROBLEM }}</textarea>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control clockpicker" value="{{ $to->START }}" name="data[{{ $i }}][START]" required>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control clockpicker" value="{{ $to->FINISH }}" name="data[{{ $i }}][FINISH]" required>
                                                </td>
                                                <td>
                                                    <select class="form-control" data-toggle="select2"
                                                        name="data[{{ $i }}][STATUS]" required>
                                                        <option value="{{ $to->UUID_STATUS }}">{{ $to->NAMA_STATUS }}</option>
                                                        @foreach ($status as $sta)
                                                        <option value="{{ $sta->UUID }}">{{ $sta->KETERANGAN }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                @php
                                                    $selectedActionBy = array_unique(explode(',', $to->ACTION_BY ?? ''));

                                                    $uniqueUsers = collect($user)
                                                        ->map(function ($item) {
                                                            $item->NRP = strtoupper(trim($item->NRP));
                                                            return $item;
                                                        })
                                                        ->keyBy('NRP')
                                                        ->values();
                                                @endphp

                                                <td>
                                                    <select class="form-control select2-multiple action-by-select"
                                                            multiple data-toggle="select2"
                                                            name="data[{{ $i }}][ACTION_BY][]"
                                                            data-placeholder="Pilih penanggung jawab baru"
                                                            style="width: 100%;">
                                                        @foreach ($uniqueUsers as $us)
                                                            <option value="{{ $us->NRP }}">{{ $us->NAMA_PANGGILAN }}</option>
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
                                                    <textarea class="form-control" name="data[{{ $i }}][REMARKS]">{{ $to->REMARKS }}</textarea>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="text-end mt-3">
                                        <button type="submit" class="btn btn-success">Update</button>
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
    $(document).ready(function() {
        $('#consumableSelect').select2({
            dropdownParent: $('#consumableModal')
        });
    });



    window.initialConsumables = @json($barangKeluar);

    window.mainConsumables = {};

    if (Array.isArray(window.initialConsumables)) {
        window.initialConsumables.forEach(item => {
            const row = item.rowIndex || 0;
            if (!mainConsumables[row]) mainConsumables[row] = [];
            mainConsumables[row].push(item);
        });
    }

    window.renderMainConsumables = function () {
        const tbody = document.querySelector('#mainConsumableTable tbody');
        if (!tbody) {
            console.error("Tbody tidak ditemukan!");
            return;
        }

        tbody.innerHTML = '';

        Object.entries(mainConsumables).forEach(([rowIdx, items]) => {
            items.forEach((c, i) => {
                tbody.innerHTML += `
                    <tr>
                        <td>${c.tower}</td>
                        <td>${c.item}</td>
                        <td>${c.qty}</td>
                        <td>
                            <button class="btn btn-danger btn-sm" onclick="window.removeFromMain(${rowIdx}, ${i})">Hapus</button>
                        </td>
                    </tr>
                `;
            });
        });

        document.getElementById('consumableData').value = JSON.stringify(mainConsumables);
        Object.entries(mainConsumables).forEach(([rowIdx, items]) => {
            const input = document.querySelector(`#consumableData_${rowIdx}`);
            if (input) {
                input.value = JSON.stringify(items);
            }
        });
    };
    window.addToModalList = function () {
        const consumableSelect = document.getElementById('consumableSelect');
        const qty = document.getElementById('consumableQty').value;
        const uuid = consumableSelect.value;
        const item = consumableSelect.options[consumableSelect.selectedIndex]?.text;

        if (!uuid || qty <= 0) {
            alert('Barang dan jumlah wajib diisi.');
            return;
        }

        window.modalConsumables.push({
            uuid,
            item,
            qty,
            tower: selectedTowerName,
            towerUUID: selectedTowerUUID
        });

        window.renderModalConsumables();

        consumableSelect.selectedIndex = 0;
        document.getElementById('consumableQty').value = '1';
    };

    window.renderModalConsumables = function () {
        const tbody = document.querySelector('#modalConsumableTable tbody');
        if (!tbody) return;
        tbody.innerHTML = '';
        window.modalConsumables.forEach((c, i) => {
            tbody.innerHTML += `
                <tr>
                    <td>${c.tower}</td>
                    <td>${c.item}</td>
                    <td>${c.qty}</td>
                    <td><button class="btn btn-danger btn-sm" onclick="window.removeFromModal(${i})">Hapus</button></td>
                </tr>
            `;
        });
    };

    window.removeFromModal = function (index) {
        window.modalConsumables.splice(index, 1);
        window.renderModalConsumables();
    };

    window.removeFromMain = function (rowIndex, index) {
        if (mainConsumables[rowIndex]) {
            mainConsumables[rowIndex].splice(index, 1);
            if (mainConsumables[rowIndex].length === 0) delete mainConsumables[rowIndex];
        }
        renderMainConsumables();
    };

    window.applyModalConsumables = function () {
        if (!mainConsumables[currentRowIndex]) {
            mainConsumables[currentRowIndex] = [];
        }

        modalConsumables.forEach(item => {
            item.rowIndex = currentRowIndex;
            mainConsumables[currentRowIndex].push(item);
        });

        renderMainConsumables();

        modalConsumables = [];
        renderModalConsumables();

        const modal = bootstrap.Modal.getInstance(document.getElementById('consumableModal'));
        modal.hide();
    };

    window.openConsumableModal = function (button) {
        const row = button.closest('tr');
        const towerSelect = row.querySelector('.tower-select');
        currentRowIndex = [...row.parentNode.children].indexOf(row);
        selectedTowerName = towerSelect.options[towerSelect.selectedIndex].text;
        selectedTowerUUID = towerSelect.value;

        modalConsumables = [];
        renderModalConsumables();

        document.getElementById('consumableModalLabel').innerText = `Tambah Consumable untuk ${selectedTowerName}`;
    };

    document.addEventListener('DOMContentLoaded', function () {
        renderMainConsumables();
    });
</script>


