
<div id="insertBarang" class="modal-demo">
    <div class="d-flex w-100 p-3 bg-primary align-items-center justify-content-between">
        <h4 class="custom-modal-title">Tambah Data</h4>
        <button type="button" class="btn-close btn-close-white" onclick="Custombox.modal.close();">
            <span class="sr-only">Close</span>
        </button>
    </div>
    <div class="custom-modal-text text-muted">
        <form action="{{ route('barang.post') }}" method="POST">
            @csrf
            <div class="mt-3">
                <div class="form-group">
                    <label for="variableForm">ITEM<span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="variableForm" name="ITEM">
                </div>
            </div>
            <div class="mt-3">
                <div class="form-group">
                    <label for="variableForm">DESCRIPTION<span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="variableForm" name="DESCRIPTION">
                </div>
            </div>
            <div class="mt-3">
                <div class="form-group">
                    <label>STATUS <span style="color: red">*</span></label>
                    <select class="form-control" name="STATUS">
                        <option value="Part">Part</option>
                         <option value="Consumable">Consumable</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <div class="form-group">
                    <label for="variableForm">STOK AWAL <span style="color: red">*</span></label>
                    <input type="number" class="form-control" id="variableForm" name="STOK_AKHIR">
                </div>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Auto-set tanggal hari ini
    const dateInput = document.getElementById('input-datepicker');
    if (dateInput) {
        const today = new Date();
        const formattedDate = today.toISOString().split('T')[0];
        dateInput.value = formattedDate;
    }

    // Cek apakah Custombox tersedia
    if (typeof Custombox !== 'undefined') {
        // Override default open event supaya kita bisa jalankan select2
        const modalTriggers = document.querySelectorAll('[data-plugin="custommodal"]');
        modalTriggers.forEach(trigger => {
            trigger.addEventListener('click', function () {
                setTimeout(() => {
                    console.log("Inisialisasi Select2 di modal...");
                    $('[data-toggle="select2"]').select2({ width: '100%' });
                }, 300); // delay agar modal muncul dulu
            });
        });
    } else {
        console.warn("Custombox tidak terdeteksi.");
    }
});
</script>


