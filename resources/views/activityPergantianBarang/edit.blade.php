@include('layout.head', ['title' => 'Edit Pergantian Barang'])
@include('layout.sidebar')
@include('layout.header')
<style>
  .hr-warning {
    height: 2px;
    background: linear-gradient(90deg, yellow, orange, red, orange, yellow);
    background-size: 300% 100%;
    border: none;
    animation: moveWarning 2s linear infinite;
  }

  @keyframes moveWarning {
    0% { background-position: 0% 50%; }
    100% { background-position: 100% 50%; }
  }
</style>
<div class="page-container">

    <div class="page-title-box">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="flex-grow-1">
                <h4 class="font-18 mb-0">Edit Pergantian Barang</h4>
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
                            <form action="{{ route('activityPergantianBarang.update', $pergantianBarang->UUID) }}" method="POST">
                                @csrf
                                <div class="form-row row">

                                    <div class="form-group col-md-3">
                                        <label for="exampleSelect1">Perangkat yang Dilepas</label>
                                        <select class="form-control" data-toggle="select2" name="UUID_BARANG_DILEPAS">
                                            <option value="{{ $pergantianBarang->UUID_PERANGKAT_DILEPAS }}">{{ $pergantianBarang->PERANGKAT_DILEPAS }}</option>
                                            @foreach ($barang as $brg1)
                                            <option value="{{ $brg1->UUID }}">{{ $brg1->ITEM }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="basic-datepicker">Tanggal Pelepasan</label>
                                        <input id="basic-datepicker" type="text" class="form-control"
                                            name="TANGGAL_PELEPASAN" value="{{ \Carbon\Carbon::parse($pergantianBarang->TANGGAL_PELEPASAN)->format('Y-m-d') }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="tempat-pengambilan">Tempat Pengambilan</label>
                                        <input id="tempat-pengambilan" type="text" class="form-control" name="POSISI_AWAL" value="{{ $pergantianBarang->POSISI_AWAL }}">
                                    </div>

                                    <!-- Select -->
                                    <div class="form-group col-md-3">
                                        <label for="sn-barang-dilepas">SN Barang Dilepas</label>
                                        <input id="sn-barang-dilepas" type="text" class="form-control" name="SN_BARANG_DILEPAS" value="{{ $pergantianBarang->SN_BARANG_DILEPAS }}">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <hr class="hr-warning">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="exampleSelect1">Perangkat yang Dipasang</label>
                                        <select class="form-control" data-toggle="select2" name="UUID_BARANG_DIPASANG">
                                            <option value="{{ $pergantianBarang->UUID_PERANGKAT_DIPASANG }}">{{ $pergantianBarang->PERANGKAT_DIPASANG }}</option>
                                            @foreach ($barang as $brg2)
                                            <option value="{{ $brg2->UUID }}">{{ $brg2->ITEM }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="basic-datepicker">Tanggal Pemasangan</label>
                                        <input id="basic-datepicker" type="text" class="form-control"
                                            name="TANGGAL_PEMASANGAN" value="{{ \Carbon\Carbon::parse($pergantianBarang->TANGGAL_PEMASANGAN)->format('Y-m-d') }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="tempat-pemasangan">Tempat Pemasangan</label>
                                        <input id="tempat-pemasangan" type="text" class="form-control" name="TUJUAN_PEMASANGAN" value="{{ $pergantianBarang->TUJUAN_PEMASANGAN }}">
                                    </div>

                                    <!-- Select -->
                                    <div class="form-group col-md-3">
                                        <label for="sn-barang-dipasang">SN Barang Dipasang</label>
                                        <input id="sn-barang-dipasang" type="text" class="form-control" name="SN_BARANG_DIPASANG" value="{{ $pergantianBarang->SN_BARANG_DIPASANG }}">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <hr class="hr-warning">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="posisi-barang-sekarang">Posisi Barang Sekarang</label>
                                        <input id="posisi-barang-sekarang" type="text" class="form-control" name="POSISI_BARANG" value="{{ $pergantianBarang->POSISI_BARANG }}">
                                    </div>
                                    <div class="form-group col-md-8">
                                        <label for="remarks">Remarks</label>
                                        <input id="remarks" type="text" class="form-control" name="REMARKS" value="{{ $pergantianBarang->REMARKS }}">
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
