@include('layout.head', ['title' => 'List Tower'])
@include('layout.sidebar')
@include('layout.header')
<div class="page-container">

                <div class="page-title-box">

                    <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-2">
                        <div class="flex-grow-1">
                            <h4 class="font-18 mb-0">List Tower</h4>
                        </div>

                        <div class="text-end">
                            <a href="#insertlistTower" class="btn btn-primary waves-effect waves-light" data-animation="sign" data-plugin="custommodal" data-overlaySpeed="100" data-overlayColor="#36404a">Insert Data</a>
                        </div>
                        @include('listTower.modal.insert')
                    </div>



                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="responsive-table-plugin">
                                    <div class="table-rep-plugin">
                                        <div class="table-responsive" data-pattern="priority-columns">
                                            <table id="tech-companies-1" class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th data-priority="1">VARIABLE</th>
                                                        <th data-priority="1">STATUS ENABLED</th>
                                                        <th data-priority="1">AKSI</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($listTower as $item)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $item->NAMA }}</td>
                                                            <td>{{ $item->STATUSENABLED == true ? '✔️' : '❌' }}</td>
                                                            <td>
                                                                <a href="#editlistTower{{ $item->UUID }}" class="btn btn-purple waves-effect waves-light btn-sm" data-animation="sign" data-plugin="custommodal" data-overlaySpeed="100" data-overlayColor="#36404a">Edit</a>
                                                            </td>
                                                        </tr>
                                                    @include('listTower.modal.edit')
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div> <!-- end .table-responsive -->

                                    </div> <!-- end .table-rep-plugin-->
                                </div> <!-- end .responsive-table-plugin-->
                            </div>
                        </div> <!-- end card-box -->
                    </div> <!-- end col -->
                </div>
                <!-- end row -->
            </div>
@include('layout.footer')
