@php
$actionByArr = array_map('trim', explode(',', $twr->ACTION_BY));
$actionByArrNRP = array_map('trim', explode(',', $twr->ACTION_BY_NRP));
$actionCount = count($actionByArr);
@endphp

<div class="modal-demo" id="editPersonil{{ $twr->UUID }}" style="background-color:transparent">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="card">

                <div class="card-body">
                    <form action="{{ route('activityTower.updatePersonil', $twr->UUID ) }}" method="post">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Personil</h5>
                        </div>
                        {{-- Jumlah personil --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jumlah Personil</label>
                            <input type="number" class="form-control action-count" min="1" max="20"
                                value="{{ $actionCount }}" data-uuid="{{ $twr->UUID }}">
                        </div>

                        {{-- Container dinamis --}}
                        <div id="actionByContainer{{ $twr->UUID }}">
                            @foreach ($actionByArr as $i => $name)
                            <div class="mb-3">
                                <label class="form-label">Personil {{ $i + 1 }}</label>
                                <select c name="" id="">

                                </select>
                                <input type="text" name="action_by[]" class="form-control"
                                    value="{{ $actionByArrNRP[$i] }}">{{ $name }}
                            </div>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>

                </div>

            </div>



        </div>
    </div>
</div>
