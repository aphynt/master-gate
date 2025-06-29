<div id="insertlistStatus" class="modal-demo">
    <div class="d-flex w-100 p-3 bg-primary align-items-center justify-content-between">
        <h4 class="custom-modal-title">Tambah Status</h4>
        <button type="button" class="btn-close btn-close-white" onclick="Custombox.modal.close();">
            <span class="sr-only">Close</span>
        </button>
    </div>
    <div class="custom-modal-text text-muted">
        <form action="{{ route('listStatus.insert') }}" method="post">
            @csrf
            <div class="mt-3">
                <div class="form-group">
                    <label for="variableForm">Variable</label>
                    <input type="text" class="form-control" id="variableForm" placeholder="Masukkan Variable" name="KETERANGAN" required>
                </div>
            </div>
            <div class="mt-3">
                <label for="variableForm">Status Enabled</label>
                <div class="form-check">
                    <input type="radio" id="statusEnabledTrue" name="STATUSENABLED" class="form-check-input" value="1" checked>
                    <label class="form-check-label" for="statusEnabledTrue">True</label>
                </div>
                <div class="form-check">
                    <input type="radio" id="statusEnabledFalse" name="STATUSENABLED" class="form-check-input" value="0">
                    <label class="form-check-label" for="statusEnabledFalse">False</label>
                </div>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
