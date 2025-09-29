<div id="editOnsite{{ $act->UUID }}" class="modal-demo">
    <div class="d-flex w-100 p-3 bg-primary align-items-center justify-content-between">
        <h4 class="custom-modal-title">Edit On-site</h4>
        <button type="button" class="btn-close btn-close-white" onclick="Custombox.modal.close();">
            <span class="sr-only">Close</span>
        </button>
    </div>
    <div class="custom-modal-text text-muted">
        <form action="{{ route('activityAdditional.update', $act->UUID) }}" method="POST">
            @csrf
            <div class="mt-3">
                <div class="form-group">
                    <select class="form-control select2-multiple" data-toggle="select2"
                        multiple name="data[0][ACTION_BY][]">
                        @foreach ($pengguna as $peng)
                        <option value="{{ $peng->NRP }}">{{ $peng->NAMA_PANGGILAN }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
