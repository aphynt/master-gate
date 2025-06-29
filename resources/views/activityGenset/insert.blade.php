@include('layout.head', ['title' => 'Insert Activity Genset'])
@include('layout.sidebar')
@include('layout.header')
<div class="page-container">

    <div class="page-title-box">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="flex-grow-1">
                <h4 class="font-18 mb-0">Insert Activity Genset</h4>
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
                            <form action="{{ route('activityGenset.post') }}" method="POST">
                                @csrf
                                <div class="form-row row">
                                    <!-- Date Report -->
                                    <div class="form-group col-md-4">
                                        <label for="basic-datepicker">Date Report <span
                                                class="text-danger">*</span></label>
                                        <input id="basic-datepicker" type="text" class="form-control"
                                            name="DATE_REPORT">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="exampleSelect1">Nama Tower <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" data-toggle="select2" name="UUID_TOWER">
                                            @foreach ($tower as $twr)
                                            <option value="{{ $twr->UUID }}">{{ $twr->NAMA }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Select -->
                                    <div class="form-group col-md-4">
                                        <label for="exampleSelect1">Kegiatan <span class="text-danger">*</span></label>
                                        <select class="form-control" data-toggle="select2" name="KEGIATAN">
                                            <option value="TURN ON">TURN ON</option>
                                            <option value="TURN OFF">TURN OFF</option>
                                        </select>
                                    </div>
                                    <div class="form-group clockpicker col-md-3" data-placement="top" data-align="top"
                                        data-autoclose="true">
                                        <label for="exampleSelect1">Start <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="09:30" name="START">
                                        {{-- <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span> --}}
                                    </div>
                                    <div class="form-group clockpicker col-md-3" data-placement="top" data-align="top"
                                        data-autoclose="true">
                                        <label for="exampleSelect2">Finish <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="13:14" name="FINISH">
                                        {{-- <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span> --}}
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="input-datepicker">Fuel (%) <span
                                                class="text-danger">*</span></label>
                                        <input id="input-datepicker" type="number" class="form-control" name="FUEL">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="input-datepicker">Action by <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control select2-multiple" data-toggle="select2"
                                                        multiple name="ACTION_BY[]">
                                                        @foreach ($user as $us)
                                                        <option value="{{ $us->NRP }}">{{ $us->NAMA_PANGGILAN }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleTextarea">Remarks</label>
                                        <textarea class="form-control" id="exampleTextarea" rows="3"
                                            name="REMARKS"></textarea>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateInput = document.getElementById('basic-datepicker');

        const makassarDate = new Intl.DateTimeFormat('en-CA', {
            timeZone: 'Asia/Makassar',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        }).format(new Date());

        dateInput.value = makassarDate;
    });

</script>
