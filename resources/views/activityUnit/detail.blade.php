@include('layout.head', ['title' => 'Show Activity Unit'])
@include('layout.sidebar')
@include('layout.header')
@php use Illuminate\Support\Str; @endphp
<style>
    th,
    td {
        font-size: 12px;
        padding: 4px;
    }

    textarea.form-control,
    select.form-control,
    input.form-control {
        font-size: 12px;
        padding: 4px;
    }

    .select2-container--default .select2-selection--single {
        height: 30px;
        font-size: 12px;
    }

    .select2-container--default .select2-selection--multiple {
        font-size: 12px;
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
    <form action="{{ route('activityUnit.update') }}" method="post">
        @csrf
        <div class="page-title-box">

            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-2">
                <div class="flex-grow-1">
                    <h4 class="font-18 mb-0">Show Activity Unit</h4>
                </div>
                <a href="{{ route('activityUnit.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
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
                                                <th style="width: 100px;">UNIT</th>
                                                <th style="width: 100px;">DATE REPORT</th>
                                                <th style="width: 120px;">ACTIVITY</th>
                                                <th style="width: 120px;">REQUEST BY</th>
                                                <th style="width: 200px;">DESCRIPTION PROBLEM</th>
                                                <th style="width: 200px;">ACTION PROBLEM</th>
                                                <th style="width: 80px;">START</th>
                                                <th style="width: 80px;">FINISH</th>
                                                <th style="width: 120px;">STATUS</th>
                                                <th style="width: 180px;">ACTION BY</th>
                                                <th style="width: 180px;">REMARKS</th>
                                            </tr>
                                        </thead>
                                        <tbody id="activity-table">
                                            @foreach ($dailyUnit as $i => $un)
                                            <tr>
                                                <td>{{ $un->NAMA_UNIT }}</td>
                                                <td>{{ $un->DATE_REPORT }}</td>
                                                <td>{{ $un->NAMA_ACTIVITY }}</td>
                                                <td>{{ $un->NAMA_REQUEST_BY }}</td>
                                                <td>{{ $un->ACTUAL_PROBLEM }}</td>
                                                <td>{{ $un->ACTION_PROBLEM }}</td>
                                                <td>{{ $un->START }}</td>
                                                <td>{{ $un->FINISH }}</td>
                                                <td>{{ $un->NAMA_STATUS }}</td>
                                                <td>{{ $un->ACTION_BY }}</td>
                                                <td>{{ $un->REMARKS }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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
                                            <th>Unit</th>
                                            <th>Barang</th>
                                            <th>Jumlah</th>
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
                        <td>${c.unit}</td>
                        <td>${c.item}</td>
                        <td>${c.qty}</td>
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
            unit: selectedUnitName,
            unitUUID: selectedUnitUUID
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
                    <td>${c.unit}</td>
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
        const unitSelect = row.querySelector('.unit-select');
        currentRowIndex = [...row.parentNode.children].indexOf(row);
        selectedUnitName = unitSelect.options[unitSelect.selectedIndex].text;
        selectedUnitUUID = unitSelect.value;

        modalConsumables = [];
        renderModalConsumables();

        document.getElementById('consumableModalLabel').innerText = `Tambah Consumable untuk ${selectedUnitName}`;
    };

    document.addEventListener('DOMContentLoaded', function () {
        renderMainConsumables();
    });
</script>


