@include('layout.head', ['title' => 'Ritation Per Hour'])
@include('layout.sidebar')
@include('layout.header')

<div class="page-container">
    <div class="page-title-box">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="flex-grow-1">
                <h4 class="font-18 mb-0">Ritation Per Hour - Edit</h4>
            </div>
            <div>
                <a href="{{ url('ritationPerHour') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-body pt-2">
                    <form id="form-tower" method="POST" action="{{ route('ritationPerHour.update') }}">
                        @csrf
                        <input type="hidden" name="DATE_REPORT" value="{{ request()->get('DATE_REPORT') }}">
                        <table class="table table-bordered" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th style="min-width: 100px;">JAM</th>
                                    <th>Total</th>
                                    <th>Realtime</th>
                                    <th>Ach</th>
                                    <th style="min-width: 500px;">Information</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($finalRitation as $index => $final)
                                    @php
                                        $ach = ($final['TOTAL'] > 0) ? $final['REALTIME'] / $final['TOTAL'] * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            {{ $final['RANGEHOUR'] }}
                                            <input type="hidden" name="data[{{ $index }}][CODE]" value="{{ $final['CODE'] }}">
                                        </td>
                                        <td>{{ $final['TOTAL'] }}</td>
                                        <td>{{ $final['REALTIME'] }}</td>
                                        <td style="{{ $ach > 0 && $ach < 95.0 ? 'color: red;' : '' }}">
                                            {{ number_format($ach, 1) }}%
                                        </td>
                                        <td>
                                            <textarea class="form-control" name="data[{{ $index }}][INFORMATION]" cols="30" rows="10">{{ $final['INFORMATION'] ?? '' }}</textarea>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">Update Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layout.footer')
