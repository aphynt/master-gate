
<div id="insertactivityGenset" class="modal-demo">
    <div class="d-flex w-100 p-3 bg-primary align-items-center justify-content-between">
        <h4 class="custom-modal-title">Tambah Data</h4>
        <button type="button" class="btn-close btn-close-white" onclick="Custombox.modal.close();">
            <span class="sr-only">Close</span>
        </button>
    </div>
    <div class="custom-modal-text text-muted">
        <form action="{{ route('activityGenset.post') }}" method="POST">
            @csrf
            <div class="mt-3">
                <div class="form-group">
                    <label>DATE REPORT <span style="color: red">*</span></label>
                    <div class="input-group ">
                        <input id="input-datepicker" type="text" class="form-control" name="DATE_REPORT" required>
                        <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                    </div>
                </div>
            </div>
            {{-- <div class="mt-3">
                <div class="form-group">
                    <label>NAMA TOWER <span style="color: red">*</span></label>
                    <select class="form-control" data-toggle="select2" name="UUID_TOWER">
                        @foreach ($tower as $twr)
                            <option value="{{ $twr->UUID }}">{{ $twr->NAMA }}</option>
                        @endforeach
                    </select>
                </div>
            </div> --}}
            <div class="mt-3">
                <div class="form-group">
                    <label>KEGIATAN <span style="color: red">*</span></label>
                    <select class="form-control" data-toggle="select2" name="KEGIATAN">
                        <option value="TURN ON">TURN ON</option>
                         <option value="TURN OFF">TURN OFF</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <div class="form-group">
                    <label for="variableForm">START <span style="color: red">*</span></label>
                    <div class="input-group ">
                        <input type="text" class="form-control" value="09:30" name="START">
                        <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <div class="form-group">
                    <label for="variableForm">FINISH <span style="color: red">*</span></label>
                    <div class="input-group " data-placement="top"
                        data-align="top" data-autoclose="true">
                        <input type="text" class="form-control" value="13:14" name="FINISH">
                        <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <div class="form-group">
                    <label for="variableForm">FUEL (%) <span style="color: red">*</span></label>
                    <input type="number" class="form-control" id="variableForm" name="FUEL">
                </div>
            </div>
            <div class="mt-3">
                <div class="form-group">
                    <label for="variableForm">REMARKS</label>
                    <textarea class="form-control" id="variableForm" style="min-height: 150px" name="REMARKS"></textarea>
                </div>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Update</button>
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


