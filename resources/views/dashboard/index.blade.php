@include('layout.head', ['title' => 'Dashboard'])
@include('layout.sidebar')
@include('layout.header')
<div class="page-container">

    <div class="page-title-box">

        <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-2">
            <div class="flex-grow-1">
                <h4 class="font-18 mb-0">Dashboard</h4>
            </div>

        </div>



    </div>

    <div class="row">
        <div class="col-md-6 col-xl-3">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="icon-layers float-end m-0 h2 text-muted"></i>
                    <h6 class="text-muted text-uppercase mt-0">Stok Barang <span class="text-success" style="font-size: 8pt">(Monthly)</span></h6>
                    <h3 class="my-3" data-plugin="counterup">{{ $dataSummary['totalBarang'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="icon-chart float-end m-0 h2 text-muted"></i>
                    <h6 class="text-muted text-uppercase mt-0">Barang Masuk <span class="text-success" style="font-size: 8pt">(Monthly)</span></h6>
                    <h3 class="my-3"><span data-plugin="counterup">{{ $dataSummary['totalBarangMasukBulanan'] }}</span></h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="icon-chart float-end m-0 h2 text-muted"></i>
                    <h6 class="text-muted text-uppercase mt-0">Barang Keluar <span class="text-success" style="font-size: 8pt">(Monthly)</span></h6>
                    <h3 class="my-3"><span data-plugin="counterup">{{ $dataSummary['totalBarangKeluarBulanan'] }}</span></h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="icon-chart float-end m-0 h2 text-muted"></i>
                    <h6 class="text-muted text-uppercase mt-0">Barang Keluar <span class="text-success" style="font-size: 8pt">(Daily)</span></h6>
                    <h3 class="my-3" data-plugin="counterup">{{ $dataSummary['totalBarangKeluarHarian'] }}</h3>
                </div>
            </div>
        </div>
    </div> <!-- end row -->
<div class="row">
        <div class="col-xl-7">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-body">
                        <h4 class="header-title mb-3">Unit yang Dimaintenance Hari Ini (Target: 7 unit/hari)</h4>

                        <p class="font-weight-semibold mb-3">{{ $dataSummary['todayMaintainedUnit'] }} <span class="text-success float-end"><b>{{ $dataSummary['todayMaintainedPercentUnit'] }}%</b></span>
                        </p>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar progress-bar-striped bg-success" role="progressbar"
                                style="width: {{ $dataSummary['todayMaintainedPercentUnit'] }}%" aria-valuenow="{{ $dataSummary['todayMaintainedPercentUnit'] }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <div class="card card-body">
                        <h4 class="header-title mb-3">Unit yang Dimaintenance Kemarin (Target: 7 unit/hari)</h4>

                        <p class="font-weight-semibold mb-2">{{ $dataSummary['yesterdayMaintainedUnit'] }} <span
                                class="text-info float-end"><b>{{ $dataSummary['yesterdayMaintainedPercentUnit'] }}%</b></span></p>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar progress-bar-striped bg-info" role="progressbar"
                                style="width: {{ $dataSummary['yesterdayMaintainedPercentUnit'] }}%" aria-valuenow="{{ $dataSummary['yesterdayMaintainedPercentUnit'] }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <div class="card card-body">
                        <h4 class="header-title mb-3">Jumlah Unit yang Dimaintenance Bulan Ini</h4>

                        <p class="font-weight-semibold mb-2">{{ $dataSummary['monthlyMaintainedUnit'] }} <span
                                class="text-dark float-end"><b>{{ $dataSummary['monthlyMaintainedPercentUnit'] }}%</b></span></p>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar progress-bar-striped bg-dark" role="progressbar"
                                style="width: {{ $dataSummary['monthlyMaintainedPercentUnit'] }}%" aria-valuenow="{{ $dataSummary['monthlyMaintainedPercentUnit'] }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <div class="card card-body">
                        <h4 class="header-title mb-3">Jumlah Unit yang Dimaintenance Bulan Lalu</h4>

                        <p class="font-weight-semibold mb-2">{{ $dataSummary['lastMonthMaintainedUnit'] }} <span
                                class="text-dark float-end"><b>{{ $dataSummary['lastMonthMaintainedPercentUnit'] }}%</b></span></p>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar progress-bar-striped bg-dark" role="progressbar"
                                style="width: {{ $dataSummary['lastMonthMaintainedPercentUnit'] }}%" aria-valuenow="{{ $dataSummary['lastMonthMaintainedPercentUnit'] }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-md-6">
                    <div class="card card-body">
                        <h4 class="header-title mb-3">Tower yang Dimaintenance Hari Ini (Target: 1 tower/hari)</h4>

                        <p class="font-weight-semibold mb-3">{{ $dataSummary['todayMaintainedTower'] }} <span class="text-success float-end"><b>{{ $dataSummary['todayMaintainedPercentTower'] }}%</b></span>
                        </p>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar progress-bar-striped bg-success" role="progressbar"
                                style="width: {{ $dataSummary['todayMaintainedPercentTower'] }}%" aria-valuenow="{{ $dataSummary['todayMaintainedPercentTower'] }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <div class="card card-body">
                        <h4 class="header-title mb-3">Tower yang Dimaintenance Kemarin (Target: 1 tower/hari)</h4>

                        <p class="font-weight-semibold mb-2">{{ $dataSummary['yesterdayMaintainedTower'] }} <span
                                class="text-info float-end"><b>{{ $dataSummary['yesterdayMaintainedPercentTower'] }}%</b></span></p>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar progress-bar-striped bg-info" role="progressbar"
                                style="width: {{ $dataSummary['yesterdayMaintainedPercentTower'] }}%" aria-valuenow="{{ $dataSummary['yesterdayMaintainedPercentTower'] }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <div class="card card-body">
                        <h4 class="header-title mb-3">Tower yang Dimaintenance Bulan Ini</h4>

                        <p class="font-weight-semibold mb-2">{{ $dataSummary['monthlyMaintainedTower'] }} <span
                                class="text-dark float-end"><b>{{ $dataSummary['monthlyMaintainedPercentTower'] }}%</b></span></p>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar progress-bar-striped bg-dark" role="progressbar"
                                style="width: {{ $dataSummary['monthlyMaintainedPercentTower'] }}%" aria-valuenow="{{ $dataSummary['monthlyMaintainedPercentTower'] }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <div class="card card-body">
                        <h4 class="header-title mb-3">Tower yang Dimaintenance Bulan Lalu</h4>

                        <p class="font-weight-semibold mb-2">{{ $dataSummary['lastMonthMaintainedTower'] }} <span
                                class="text-dark float-end"><b>{{ $dataSummary['lastMonthMaintainedPercentTower'] }}%</b></span></p>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar progress-bar-striped bg-dark" role="progressbar"
                                style="width: {{ $dataSummary['lastMonthMaintainedPercentTower'] }}%" aria-valuenow="{{ $dataSummary['lastMonthMaintainedPercentTower'] }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                </div>



            </div>
        </div><!-- end col-->

        <div class="col-xl-5">
            <div class="card card-body">

                <h4 class="header-title mb-3">Status Unit</h4>

                <div class="table-responsive">
                    <table class="table table-bordered table-nowrap mb-0">
                        <thead>
                                <tr>
                                    <th>Status</th>
                                    <th style="text-align: center">EX</th>
                                    <th style="text-align: center">HD</th>
                                    <th style="text-align: center">MG</th>
                                    <th style="text-align: center">BD</th>
                                    <th style="text-align: center">WT</th>
                                    <th style="text-align: center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(['Operasi', 'Standby', 'Breakdown'] as $status)
                                    <tr>
                                        <td>{{ $status }}</td>

                                        <!-- Tipe kendaraan -->
                                        @foreach(['EX', 'HD', 'MG', 'BD', 'WT'] as $type)
                                            <td style="text-align: center">
                                                {{
                                                    $dataSummary['statusUnit']->where('VSA_GROUPDESC', $status)
                                                               ->where('VHC_TYPE', $type)
                                                               ->sum('NDATA')
                                                }}
                                            </td>
                                        @endforeach

                                        <!-- Total per status -->
                                        <td style="text-align: center">
                                            {{
                                                $dataSummary['statusUnit']->where('VSA_GROUPDESC', $status)
                                                           ->sum('NDATA')
                                            }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <!-- Total keseluruhan -->
                            <tfoot>
                                <tr>
                                    <td>Total</td>
                                    @foreach(['EX', 'HD', 'MG', 'BD', 'WT'] as $type)
                                        <td style="text-align: center">
                                            {{
                                                $dataSummary['statusUnit']->where('VHC_TYPE', $type)
                                                           ->sum('NDATA')
                                            }}
                                        </td>
                                    @endforeach
                                    <td style="text-align: center">
                                        {{
                                            $dataSummary['statusUnit']->sum('NDATA')
                                        }}
                                    </td>
                                </tr>
                            </tfoot>
                    </table>
                </div>
            </div>
        </div><!-- end col-->

    </div>
    {{-- <div class="row">
        <div class="col-lg-6 col-xl-8">
            <div class="card card-body">
                <h4 class="header-title mb-3">Sales Statistics</h4>

                <div class="text-center">
                    <ul class="list-inline chart-detail-list mb-0">
                        <li class="list-inline-item">
                            <h6 class="text-info"><i class="mdi mdi-circle-outline me-1"></i>Series A</h6>
                        </li>
                        <li class="list-inline-item">
                            <h6 class="text-success"><i class="mdi mdi-triangle-outline me-1"></i>Series B</h6>
                        </li>
                        <li class="list-inline-item">
                            <h6 class="text-muted"><i class="mdi mdi-square-outline me-1"></i>Series C</h6>
                        </li>
                    </ul>
                </div>

                <div id="morris-bar-stacked" class="morris-chart" style="height: 320px;"></div>

            </div>
        </div><!-- end col-->

        <div class="col-lg-6 col-xl-4">
            <div class="card card-body">
                <h4 class="header-title mb-3">Trends Monthly</h4>

                <div class="text-center mb-3">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-sm btn-secondary">Today</button>
                        <button type="button" class="btn btn-sm btn-secondary">This Week</button>
                        <button type="button" class="btn btn-sm btn-secondary">Last Week</button>
                    </div>
                </div>

                <div id="morris-donut-example" class="morris-chart" style="height: 268px;"></div>

                <div class="text-center">
                    <ul class="list-inline chart-detail-list mb-0 mt-2">
                        <li class="list-inline-item">
                            <h6 class="text-info"><i class="mdi mdi-circle-outline me-1"></i>English</h6>
                        </li>
                        <li class="list-inline-item">
                            <h6 class="text-success"><i class="mdi mdi-triangle-outline me-1"></i>Italian</h6>
                        </li>
                        <li class="list-inline-item">
                            <h6 class="text-muted"><i class="mdi mdi-square-outline me-1"></i>French</h6>
                        </li>
                    </ul>
                </div>
            </div>
        </div><!-- end col-->
    </div> --}}

     <!-- end row -->

</div>
@include('layout.footer')
