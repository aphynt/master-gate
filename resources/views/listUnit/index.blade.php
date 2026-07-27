@include('layout.head', ['title' => 'List Unit'])
@include('layout.sidebar')
@include('layout.header')
<div class="page-container">

                <div class="page-title-box">

                    <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-2">
                        <div class="flex-grow-1">
                            <h4 class="font-18 mb-0">List Unit</h4>
                        </div>

                       <form action="" method="GET" class="d-flex align-items-center gap-2">
                            <button type="submit" name="action_type" value="export" class="btn btn-primary waves-effect waves-light">
                                Export Excel
                            </button>
                        </form>
                    </div>



                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="responsive-table-plugin">
                                    <div class="table-rep-plugin">
                                        <div class="dt-responsive table-responsive" >
                                            <table id="datatable" class="table table-bordered dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th data-priority="1">VEHICLE</th>
                                                        <th data-priority="1">GROUP</th>
                                                        <th data-priority="1">TYPE</th>
                                                        <th data-priority="1">IP ADDRESS</th>
                                                        <th data-priority="1">STEP DOWN DC TO DC</th>
                                                        <th data-priority="1">STATUS ENABLED</th>
                                                        <th data-priority="1">AKSI</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($listUnit as $item)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $item->VHC_ID }}</td>
                                                            <td>{{ $item->GROUP_ID }}</td>
                                                            <td>{{ $item->TYPE_ID }}</td>
                                                            <td>{{ $item->IP_ADDRESS }}</td>
                                                            <td>{{ $item->CONVERTER_DC_TO_DC == true ? '✔️' : '🟡' }}</td>
                                                            <td>{{ $item->STATUSENABLED == true ? '✔️' : '❌' }}</td>
                                                            <td>
                                                                <button
                                                                    type="button"
                                                                    class="btn btn-purple btn-sm"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#editModal"

                                                                    data-uuid="{{ $item->UUID }}"
                                                                    data-vhc="{{ $item->VHC_ID }}"
                                                                    data-converter="{{ $item->CONVERTER_DC_TO_DC }}"
                                                                    data-status="{{ $item->STATUSENABLED }}">
                                                                    Edit
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            @include('listUnit.modal.edit')
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

<script>
    $('#datatable').DataTable({
    pageLength: 50,
    lengthMenu: [
        [10, 25, 50, 100, -1],
        [10, 25, 50, 100, "All"]
    ]
});

$('#editModal').on('show.bs.modal', function (event) {

    let button = $(event.relatedTarget);

    let uuid = button.data('uuid');
    let vhc = button.data('vhc');
    let converter = button.data('converter');
    let status = button.data('status');

    $('#vhc').val(vhc);

    $('input[name="CONVERTER_DC_TO_DC"][value="' + converter + '"]')
        .prop('checked', true);

    $('input[name="STATUSENABLED"][value="' + status + '"]')
        .prop('checked', true);

    $('#editForm').attr('action', '/listUnit/update/' + uuid);

});
</script>
