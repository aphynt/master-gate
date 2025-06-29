@include('layout.head', ['title' => 'Edit Inventory Incoming'])
@include('layout.sidebar')
@include('layout.header')
<div class="page-container">

    <div class="page-title-box">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="flex-grow-1">
                <h4 class="font-18 mb-0">Edit Inventory Incoming</h4>
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
                            <form action="{{ route('barangMasuk.update', $barangMasuk->UUID) }}" method="POST">
                                @csrf
                                <div class="form-row row">
                                    <!-- Date Report -->
                                    <div class="form-group col-md-3">
                                        <label for="basic-datepicker">Tanggal Masuk<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="TANGGAL_MASUK" value="{{ \Carbon\Carbon::parse($barangMasuk->TANGGAL_MASUK)->format('Y-m-d') }}" readonly required>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="exampleSelect1">Nama Barang <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="UUID_BARANG" value="{{ $barangMasuk->NAMA_BARANG }}" readonly required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="input-datepicker">Jumlah <span
                                                class="text-danger">*</span></label>
                                        <input id="input-datepicker" type="number" class="form-control" name="JUMLAH" value="{{ $barangMasuk->JUMLAH }}" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="exampleSelect1">PIC <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="PIC" value="{{ $barangMasuk->NAMA_PIC }}" readonly required>
                                    </div>
                                    <!-- Select -->
                                    <div class="form-group">
                                        <label for="exampleTextarea">Keterangan</label>
                                        <textarea class="form-control" id="exampleTextarea" rows="3"
                                            name="KETERANGAN">{{ $barangMasuk->KETERANGAN }}</textarea>
                                    </div>


                                </div>
                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-primary">Update</button>
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
