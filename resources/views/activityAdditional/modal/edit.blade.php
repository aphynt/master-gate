<div id="editlistActivity{{ $act->UUID }}" class="modal-demo">
    <div class="d-flex w-100 p-3 bg-primary align-items-center justify-content-between">
        <h4 class="custom-modal-title">Edit Activity Additional</h4>
        <button type="button" class="btn-close btn-close-white" onclick="Custombox.modal.close();">
            <span class="sr-only">Close</span>
        </button>
    </div>
    <div class="custom-modal-text text-muted">
        <form action="{{ route('activityAdditional.update', $act->UUID) }}" method="POST">
            @csrf
            <div class="mt-3">
                <div class="form-group">
                    <label for="variableForm">TEAM</label>
                    <select class="form-control" name="TEAM" required>
                                <option value="{{ $act->UUID_TEAM }}">{{ $act->NAMA_TEAM }}</option>
                                @foreach ($team as $te)
                                <option value="{{ $te->UUID }}">{{ $te->NAMA }}</option>
                                @endforeach
                            </select>
                </div>
            </div>
            <div class="mt-3">
                <div class="form-group">
                    <label>START</label>
                    <div class="input-group clockpicker">
                        <input type="text" class="form-control"
                            value="{{ \Carbon\Carbon::parse($act->START)->format('H:i') }}" name="START" required>
                        <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <div class="form-group">
                    <label>FINISH</label>
                    <div class="input-group clockpicker">
                        <input type="text" class="form-control"
                            value="{{ \Carbon\Carbon::parse($act->FINISH)->format('H:i') }}" name="FINISH" required>
                        <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <div class="form-group">
                    <label for="variableForm">ACTIVITY</label>
                    <textarea class="form-control" id="variableForm" style="min-height: 200px" name="ACTION_PROBLEM"
                        required>{{ $act->ACTION_PROBLEM }}</textarea>
                </div>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
