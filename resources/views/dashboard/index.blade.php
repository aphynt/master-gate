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
                    <h6 class="text-muted text-uppercase mt-0">Orders</h6>
                    <h3 class="my-3" data-plugin="counterup">1,587</h3>
                    <span class="badge bg-success me-1"> +11% </span> <span class="text-muted">From previous
                        period</span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="icon-paypal float-end m-0 h2 text-muted"></i>
                    <h6 class="text-muted text-uppercase mt-0">Revenue</h6>
                    <h3 class="my-3">$<span data-plugin="counterup">46,782</span></h3>
                    <span class="badge bg-danger me-1"> -29% </span> <span class="text-muted">From previous
                        period</span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="icon-chart float-end m-0 h2 text-muted"></i>
                    <h6 class="text-muted text-uppercase mt-0">Average Price</h6>
                    <h3 class="my-3">$<span data-plugin="counterup">15.9</span></h3>
                    <span class="badge bg-danger me-1"> 0% </span> <span class="text-muted">From previous
                        period</span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class="icon-rocket float-end m-0 h2 text-muted"></i>
                    <h6 class="text-muted text-uppercase mt-0">Product Sold</h6>
                    <h3 class="my-3" data-plugin="counterup">1,890</h3>
                    <span class="badge bg-warning me-1"> +89% </span> <span class="text-muted">Last year</span>
                </div>
            </div>
        </div>
    </div> <!-- end row -->

    <div class="row">
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
    </div> <!-- end row -->

    <div class="row">
        <div class="col-xl-7">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="header-title mb-3">Inbox</h4>
                        </div>

                        <div class="inbox-widget px-4" data-simplebar style="max-height: 332px;">
                            <a href="#">
                                <div class="d-flex align-items-center gap-2 py-2">
                                    <div class="inbox-item-img"><img
                                            src="{{ asset('dashboard') }}/assets/images/users/avatar-1.jpg"
                                            class="rounded-circle avatar-lg" alt=""></div>
                                    <div class="me-auto">
                                        <p class="text-body mb-0">Chadengle</p>
                                        <p class="text-muted mb-0">Hey! there I'm available...</p>
                                    </div>
                                    <p class="font-12 text-body">13:40 PM</p>
                                </div>
                            </a>
                            <a href="#">
                                <div class="d-flex align-items-center gap-2 py-2">
                                    <div class="inbox-item-img"><img
                                            src="{{ asset('dashboard') }}/assets/images/users/avatar-2.jpg"
                                            class="rounded-circle avatar-lg" alt=""></div>
                                    <div class="me-auto">
                                        <p class="text-body mb-0">Tomaslau</p>
                                        <p class="text-muted mb-0 text-truncate">I've finished it! See you so...
                                        </p>
                                    </div>
                                    <p class="font-12 text-body">13:34 PM</p>
                                </div>
                            </a>
                            <a href="#">
                                <div class="d-flex align-items-center gap-2 py-2">
                                    <div class="inbox-item-img"><img
                                            src="{{ asset('dashboard') }}/assets/images/users/avatar-3.jpg"
                                            class="rounded-circle avatar-lg" alt=""></div>
                                    <div class="me-auto">
                                        <p class="text-body mb-0">Stillnotdavid</p>
                                        <p class="text-muted mb-0">This theme is awesome!</p>
                                    </div>
                                    <p class="font-12 text-body">13:17 PM</p>
                                </div>
                            </a>
                            <a href="#">
                                <div class="d-flex align-items-center gap-2 py-2">
                                    <div class="inbox-item-img"><img
                                            src="{{ asset('dashboard') }}/assets/images/users/avatar-4.jpg"
                                            class="rounded-circle avatar-lg" alt=""></div>
                                    <div class="me-auto">
                                        <p class="text-body mb-0">Kurafire</p>
                                        <p class="text-muted mb-0">Nice to meet you</p>
                                    </div>
                                    <p class="font-12 text-body">12:20 PM</p>
                                </div>
                            </a>
                            <a href="#">
                                <div class="d-flex align-items-center gap-2 py-2">
                                    <div class="inbox-item-img"><img
                                            src="{{ asset('dashboard') }}/assets/images/users/avatar-5.jpg"
                                            class="rounded-circle avatar-lg" alt=""></div>
                                    <div class="me-auto">
                                        <p class="text-body mb-0">Shahedk</p>
                                        <p class="text-muted mb-0">Hey! there I'm available...</p>
                                    </div>
                                    <p class="font-12 text-body">10:15 AM</p>
                                </div>
                            </a>
                            <a href="#">
                                <div class="d-flex align-items-center gap-2 py-2">
                                    <div class="inbox-item-img"><img
                                            src="{{ asset('dashboard') }}/assets/images/users/avatar-6.jpg"
                                            class="rounded-circle avatar-lg" alt=""></div>
                                    <div class="me-auto">
                                        <p class="text-body mb-0">Adhamdannaway</p>
                                        <p class="text-muted mb-0">This theme is awesome!</p>
                                    </div>
                                    <p class="font-12 text-body">9:56 AM</p>
                                </div>
                            </a>
                            <a href="#">
                                <div class="d-flex align-items-center gap-2 py-2">
                                    <div class="inbox-item-img"><img
                                            src="{{ asset('dashboard') }}/assets/images/users/avatar-8.jpg"
                                            class="rounded-circle avatar-lg" alt=""></div>
                                    <div class="me-auto">
                                        <p class="text-body mb-0">Arashasghari</p>
                                        <p class="text-muted mb-0">Hey! there I'm available...</p>
                                    </div>
                                    <p class="font-12 text-body">10:15 AM</p>
                                </div>
                            </a>
                            <a href="#">
                                <div class="d-flex align-items-center gap-2 py-2">
                                    <div class="inbox-item-img"><img
                                            src="{{ asset('dashboard') }}/assets/images/users/avatar-9.jpg"
                                            class="rounded-circle avatar-lg" alt=""></div>
                                    <div class="me-auto">
                                        <p class="text-body mb-0">Joshaustin</p>
                                        <p class="text-muted mb-0">I've finished it! See you so...</p>
                                    </div>
                                    <p class="font-12 text-body">9:56 AM</p>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card card-body">
                        <h4 class="header-title mb-3">Sales Statistics</h4>

                        <p class="font-weight-semibold mb-3">iMacs <span class="text-danger float-end"><b>78%</b></span>
                        </p>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar progress-bar-striped bg-danger" role="progressbar"
                                style="width: 78%" aria-valuenow="78" aria-valuemin="0" aria-valuemax="78">
                            </div>
                        </div>
                    </div>

                    <div class="card card-body">
                        <h4 class="header-title mb-3">Monthly Sales</h4>

                        <p class="font-weight-semibold mb-2">Macbooks <span
                                class="text-success float-end"><b>25%</b></span></p>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar progress-bar-striped bg-success" role="progressbar"
                                style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <div class="card card-body">
                        <h4 class="header-title mb-3">Daily Sales</h4>

                        <p class="font-weight-semibold mb-2">Mobiles <span
                                class="text-warning float-end"><b>75%</b></span></p>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar progress-bar-striped bg-warning" role="progressbar"
                                style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div><!-- end col-->

        <div class="col-xl-5">
            <div class="card card-body">

                <h4 class="header-title mb-3">Top Contracts</h4>

                <div class="table-responsive">
                    <table class="table table-bordered table-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th class="text-muted">Apple Technology</th>
                                <td>20/02/2014</td>
                                <td>19/02/2020</td>
                                <td><span class="badge bg-success">Paid</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Envato Pty Ltd.</th>
                                <td>20/02/2014</td>
                                <td>19/02/2020</td>
                                <td><span class="badge bg-danger">Unpaid</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Dribbble LLC.</th>
                                <td>20/02/2014</td>
                                <td>19/02/2020</td>
                                <td><span class="badge bg-success">Paid</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Adobe Family</th>
                                <td>20/02/2014</td>
                                <td>19/02/2020</td>
                                <td><span class="badge bg-success">Paid</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Apple Technology</th>
                                <td>20/02/2014</td>
                                <td>19/02/2020</td>
                                <td><span class="badge bg-danger">Unpaid</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Envato Pty Ltd.</th>
                                <td>20/02/2014</td>
                                <td>19/02/2020</td>
                                <td><span class="badge bg-success">Paid</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div><!-- end col-->

    </div> <!-- end row -->

</div>
@include('layout.footer')
