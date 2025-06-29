@include('layout.head', ['title' => 'Inventory Incoming'])
@include('layout.sidebar')
@include('layout.header')
<div class="page-container">

    <div class="page-title-box">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="flex-grow-1">
                <h4 class="font-18 mb-0">Inventory Incoming</h4>
            </div>

            <div>
                <a href="{{ route('barangMasuk.insert') }}" class="btn btn-primary waves-effect waves-light">
                    Insert Data
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pt-2">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th data-priority="1">NAMA BARANG</th>
                                <th data-priority="1">TANGGAL MASUK</th>
                                <th data-priority="1">JUMLAH</th>
                                <th data-priority="1">PIC</th>
                                <th data-priority="1">KETERANGAN</th>
                                <th data-priority="1">REPORTING</th>
                                <th data-priority="6">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($barangMasuk as $brm)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $brm->NAMA_BARANG }}</td>
                                <td>{{ \Carbon\Carbon::parse($brm->TANGGAL_MASUK)->format('Y-m-d') }}</td>
                                <td>{{ $brm->JUMLAH }}</td>
                                <td>{{ $brm->PIC }}</td>
                                <td>{{ $brm->KETERANGAN }}</td>
                                <td>{{ $brm->NAMA_REPORTING }}</td>
                                <td>
                                    @if (Auth::user()->nrp == $brm->NRP_REPORTING)

                                        <a href="{{ route('barangMasuk.edit', $brm->UUID) }}" class="btn btn-purple waves-effect waves-light btn-sm">Edit</a>

                                        <a href="#deleteBarangMasuk{{ $brm->UUID }}"
                                            class="btn btn-danger waves-effect waves-light btn-sm" data-animation="contentscale"
                                            data-plugin="custommodal" data-overlaySpeed="100"
                                            data-overlayColor="#36404a">Hapus</a>
                                            @include('barangMasuk.modal.delete')
                                    @endif
                                </td>
                            </tr>

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layout.footer')
