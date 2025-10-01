@include('layout.head', ['title' => 'All Summary'])
@include('layout.sidebar')
@include('layout.header')
<div class="page-container">

    <div class="page-title-box">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="flex-grow-1">
                <h4 class="font-18 mb-0">All Summary</h4>
            </div>
        </div>
    </div>

    <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="dt-responsive table-responsive">
                            <table id="allSummary" class="table table-striped table-hover table-bordered nowrap">
                                <thead style="text-align: center; vertical-align: middle;">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Team</th>
                                        <th>Activity</th>
                                        <th>Start</th>
                                        <th>Finish</th>
                                        <th>On-site</th>
                                        <th>Reporting</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</div>


@include('layout.footer')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateInput = document.getElementById('basic-datepicker');

        // Ambil parameter dari URL
        const params = new URLSearchParams(window.location.search);
        const dateReport = params.get('DATE_REPORT');

        if (dateReport) {
            dateInput.value = dateReport;
        } else {
            // Buat tanggal sekarang dalam zona waktu Asia/Makassar
            const now = new Date();

            // Format sebagai YYYY-MM-DD di Asia/Makassar
            const makassarDate = new Intl.DateTimeFormat('en-CA', {
                timeZone: 'Asia/Makassar',
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            }).format(now);

            dateInput.value = makassarDate;
        }
    });
</script>

<script>
    $(document).ready(function() {
        var userRole = "{{ Auth::user()->role }}";
        var table = $('#allSummary').DataTable({

            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('allActivity.api') }}',
                method: 'GET',
                data: function(d) {
                    d.DATE_REPORT = $('#basic-datepicker').val();
                    delete d.columns;
                    delete d.order;
                },
            },
            columns: [
                {
                    data: 'DATE_REPORT',
                    render: function(data) {
                        if (!data) return '';
                        let date = new Date(data);
                        return date.toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'long',
                            year: 'numeric'
                        });
                    }
                },
                { data: 'TEAM' },
                { data: 'ACTIVITY' },
                {
                    data: 'START',
                    render: function(data) {
                        if (!data) return '';
                        return data.substring(0,5);
                    }
                },
                {
                    data: 'FINISH',
                    render: function(data) {
                        if (!data) return '';
                        return data.substring(0,5);
                    }
                },
                { data: 'PIC' },
                { data: 'REPORTING' },
            ],
            "order": [[0, "desc"]],
            "pageLength": 20,
            "lengthMenu": [10, 20, 25, 50],
        });

        $('#refreshButton').click(function() {
            table.ajax.reload();
        });
    });

</script>
