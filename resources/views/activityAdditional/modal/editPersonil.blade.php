@php
$actionByArr = array_map('trim', explode(',', $act->ACTION_BY));
$actionByArrNRP = array_map('trim', explode(',', $act->ACTION_BY_NRP));
$actionCount = count($actionByArr);
@endphp

<div class="modal-demo" id="editPersonil{{ $act->UUID }}" style="background-color:transparent">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="card">

                <div class="card-body">
                    <form action="{{ route('activityAdditional.updatePersonil', $act->UUID ) }}" method="post" id="formPersonil-{{ $act->UUID }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Personil</h5>
                        </div>

                        {{-- Container dinamis --}}
                        <div id="actionByContainer{{ $act->UUID }}">
                            @foreach ($actionByArr as $i => $name)
                            <div class="mb-3 d-flex align-items-center action-row">
                                <div class="flex-grow-1 me-2">
                                    <label class="form-label">Personil {{ $i + 1 }}</label>
                                    <select class="form-select" name="action_by[{{ $act->UUID }}][]">
                                        <option value="{{ $actionByArrNRP[$i] }}">{{ $name }}</option>
                                        @foreach ($user as $uss)
                                            <option value="{{ $uss->NRP }}">{{ $uss->NAME }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm remove-action mt-4">
                                    Hapus
                                </button>
                            </div>
                            @endforeach
                        </div>

                        <button type="button"
                                class="btn btn-success btn-sm mt-2 add-action"
                                data-uuid="{{ $act->UUID }}">
                            + Tambah Personil
                        </button>
                        <div class="modal-footer">
                            <button type="submit"
                                    class="btn btn-primary save-personil"
                                    form="formPersonil-{{ $act->UUID }}">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>

                </div>

            </div>



        </div>
    </div>
</div>
