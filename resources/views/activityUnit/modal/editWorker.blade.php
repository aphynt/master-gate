<div class="modal-demo" id="editWorkerlistActivity{{ $unt->ID }}">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <form id="formUpdateWorker{{ $unt->ID }}"
                  action="{{ route('activityUnit.updateWorker', $unt->ID) }}"
                  method="POST">
                @csrf
                <input type="hidden" name="ACTION_BY" id="hiddenWorker{{ $unt->ID }}">

                <div class="modal-body p-5">
                    <div class="form-group">
                        <label class="form-label text-start d-block">
                            Pilih ulang Worker di {{ $unt->NAMA_UNIT }}
                        </label>

                        <select id="workerSelect{{ $unt->ID }}"
                                class="form-control select2-multiple" data-toggle="select2"
                                multiple>
                            @foreach ($user as $us)
                                <option value="{{ $us->NRP }}">{{ $us->NAMA_PANGGILAN }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="javascript:void(0)"
                       class="btn btn-primary submit-worker-btn"
                       data-id="{{ $unt->ID }}">
                       Update
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".submit-worker-btn").forEach(btn => {
        btn.addEventListener("click", function() {
            let id = this.dataset.id;
            let formId = "formUpdateWorker" + id;
            console.log("Cari form dengan ID:", formId);

            let form = document.getElementById(formId);
            console.log("Hasil form:", form);

            if (!form) {
                alert("Form tidak ditemukan untuk ID: " + id);
                return;
            }

            let select = form.querySelector("select");
            if (!select) {
                alert("Select tidak ditemukan dalam form!");
                return;
            }

            let selected = Array.from(select.selectedOptions).map(opt => opt.value);
            if (selected.length === 0) {
                alert("Pilih minimal 1 worker!");
                return;
            }

            form.querySelector("input[name=ACTION_BY]").value = JSON.stringify(selected);
            form.submit();
        });
    });
});
</script>
