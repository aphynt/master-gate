@include('layout.head', ['title' => 'Inventory'])
@include('layout.sidebar')
@include('layout.header')
<div class="page-container">

    <div class="page-title-box">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="flex-grow-1">
                <h4 class="font-18 mb-0">Inventory</h4>
            </div>

            <div>
                <a href="#insertBarang" class="btn btn-primary waves-effect waves-light"
                data-animation="contentscale"
                data-plugin="custommodal" data-overlaySpeed="100"
                data-overlayColor="#36404a">
                    Insert Data
                </a>
                @include('barang.modal.insert')
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
                                <th data-priority="1">UNIQUE KEY</th>
                                <th data-priority="1">ITEM</th>
                                <th data-priority="1">DESCRIPTION</th>
                                <th data-priority="1">STATUS</th>
                                <th data-priority="1">STOK AWAL AGUSTUS 2025</th>
                                <th data-priority="1">STOK AKHIR</th>
                                <th data-priority="6">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                           @foreach ($barang as $brg)
                            @php
                                $masuk = $barangMasuk[$brg->UUID]->total_masuk ?? 0;
                                $keluar = $barangKeluar[$brg->UUID]->total_keluar ?? 0;
                                $stokAwal = $brg->STOK_AKHIR ?? 0;
                                $stokAkhir = $stokAwal + $masuk - $keluar;
                            @endphp
                            <tr style="{{ $stokAkhir < 0 ? 'background-color: #fff3cd;' : '' }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Illuminate\Support\Str::substr($brg->UUID, 0, 8) }}</td>
                                <td>{{ $brg->ITEM }}</td>
                                <td>{{ $brg->DESCRIPTION }}</td>
                                <td>{{ $brg->STATUS }}</td>
                                <td>{{ $stokAwal }}</td>
                                <td>@if ($brg->STATUS != 'Consumable')
                                        {{ $stokAkhir }}
                                @endif
                            </td>
                                <td>
                                    @if ($brg->ADD_BY == Auth::user()->nrp)
                                    <a href="#deleteBarang{{ $brg->UUID }}" class="btn btn-danger btn-sm"
                                        data-plugin="custommodal" data-overlaySpeed="100"
                                        data-overlayColor="#36404a">Hapus</a>
                                    @include('barang.modal.delete')
                                    @else
                                    -
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
