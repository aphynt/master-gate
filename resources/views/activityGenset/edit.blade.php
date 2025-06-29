@include('layout.head', ['title' => 'Update Activity Genset'])
@include('layout.sidebar')
@include('layout.header')
<div class="page-container">

    <div class="page-title-box">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="flex-grow-1">
                <h4 class="font-18 mb-0">Update Activity Genset</h4>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    {{-- <h5 class="card-title">Input Types</h5> --}}
                </div>

                <div class="card-body pt-2">
                    <div class="row">
                        <div class="col-xl-12">
                            <form action="{{ route('activityGenset.update', $activity->UUID) }}" method="POST">
                                @csrf
                                <div class="form-row row">
                                    <!-- Date Report -->
                                    <div class="form-group col-md-4">
                                        <label for="basic-datepicker">Date Report <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="START" value="{{ \Carbon\Carbon::parse($activity->DATE_REPORT)->format('Y-m-d') }}" readonly>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="exampleSelect1">Nama Tower <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="UUID_TOWER" value="{{ $activity->NAMA_TOWER }}" readonly>
                                    </div>

                                    <!-- Select -->
                                    <div class="form-group col-md-4">
                                        <label for="exampleSelect1">Kegiatan <span class="text-danger">*</span></label>
                                        <select class="form-control" data-toggle="select2" name="KEGIATAN">
                                            <option value="{{ $activity->KEGIATAN }}" selected>{{ $activity->KEGIATAN }}</option>
                                            <option value="TURN ON">TURN ON</option>
                                            <option value="TURN OFF">TURN OFF</option>
                                        </select>
                                    </div>
                                    <div class="form-group clockpicker col-md-4" data-placement="top" data-align="top"
                                        data-autoclose="true">
                                        <label for="exampleSelect1">Start <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="START" value="{{ \Carbon\Carbon::parse($activity->START)->format('H:i') }}">
                                        {{-- <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span> --}}
                                    </div>
                                    <div class="form-group clockpicker col-md-4" data-placement="top" data-align="top"
                                        data-autoclose="true">
                                        <label for="exampleSelect2">Finish <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="FINISH" value="{{ \Carbon\Carbon::parse($activity->FINISH)->format('H:i') }}">
                                        {{-- <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span> --}}
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="input-datepicker">Fuel (%) <span
                                                class="text-danger">*</span></label>
                                        <input id="input-datepicker" type="number" class="form-control" name="FUEL" value="{{ $activity->FUEL }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleTextarea">Remarks</label>
                                        <textarea class="form-control" id="exampleTextarea" rows="3"
                                            name="REMARKS">{{ $activity->REMARKS }}</textarea>
                                    </div>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div><!-- end col -->

                    </div><!-- end row -->
                    <!-- end row-->
                </div> <!-- end card-body -->
            </div> <!-- end card -->
        </div><!-- end col -->
    </div>
</div>

@include('layout.footer')
