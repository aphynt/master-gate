@include('layout.head', ['title' => 'Summary Ritation per Monthly'])
@include('layout.sidebar')
@include('layout.header')

<style>
    .sticky-col {
        position: sticky;
        left: 0;
        z-index: 2;
    }

    /* Thead tetap warna header */
    thead .sticky-col {
        z-index: 3;
        background-color: #cfe2ff;
    }

    .table-sm>:not(caption)>*>* {
        padding: .1rem .3rem;
    }

    tbody tr:nth-child(odd) .sticky-col {
        background-color: #ffffff;
    }

    tbody tr:nth-child(even) .sticky-col {
        background-color: #f9f9f9;
    }

    /* Footer total, selisih, shift */
    tfoot tr.table-success .sticky-col {
        background-color: #d1e7dd;
    }

    tfoot tr.table-warning .sticky-col {
        background-color: #fff3cd;
    }

    tfoot tr.table-dark .sticky-col {
        background-color: #212529;
        color: white;
    }

    tfoot tr:not(.table-success):not(.table-warning):not(.table-dark) .sticky-col {
        background-color: #ffffff;
    }

</style>

<div class="page-container">

    <div class="page-title-box">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="flex-grow-1">
                <h4 class="font-18 mb-0">Summary Ritation per Monthly</h4>
            </div>

            <form action="" method="GET" class="d-flex align-items-center gap-2">
                <input type="month" name="DATE_REPORT" class="form-control" required style="width: 160px;">
                <button type="submit" class="btn btn-outline-primary">Show Report</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body" style="padding: 1rem;">
                    <div style="overflow-x: auto; white-space: nowrap;">
                        <table class="table table-bordered table-striped table-sm text-center align-middle"
                            style="min-width: max-content;">
                            <thead class="table-primary">
                                <tr>
                                    <th class="sticky-col">Jam</th>
                                    @foreach($groupedRitation as $date => $records)
                                    <th colspan="4">{{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</th>
                                    @endforeach
                                    <th colspan="3" class="bg-info">Grand Total</th>
                                </tr>
                                <tr>
                                    <th class="sticky-col"></th>
                                    @foreach($groupedRitation as $date => $records)
                                    <th>Total</th>
                                    <th>Realtime</th>
                                    <th>Ach</th>
                                    <th>Information</th>
                                    @endforeach
                                    <th>Total</th>
                                    <th>Realtime</th>
                                    <th>Ach</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $allHours = collect($groupedRitation)
                                ->flatten(1)
                                ->pluck('RANGEHOUR')
                                ->unique()
                                ->sort(function($a, $b) {
                                $toHour = fn($time) => (int) explode(':', $time)[0];
                                $ha = $toHour($a); $hb = $toHour($b);
                                $ha = $ha < 7 ? $ha + 24 : $ha; $hb=$hb < 7 ? $hb + 24 : $hb; return $ha <=> $hb;
                                    })
                                    ->values();

                                    $totals = [];
                                    $realtimes = [];
                                    $siangTotals = [];
                                    $siangRealtimes = [];
                                    $malamTotals = [];
                                    $malamRealtimes = [];
                                    @endphp

                                    @foreach($allHours as $hour)
                                    <tr>
                                        <td class="sticky-col">{{ $hour }}</td>
                                        @php
                                        $rowTotalAllDates = 0;
                                        $rowRealtimeAllDates = 0;
                                        $achValueAllDates = 0;
                                        $rowAchAllDates = 0;
                                        @endphp
                                        @foreach($groupedRitation as $date => $records)
                                        @php
                                        $row = collect($records)->firstWhere('RANGEHOUR', $hour);
                                        $total = $row['TOTAL'] ?? 0;
                                        $rt = $row['REALTIME'] ?? 0;
                                        $achValue = ($total > 0) ? round(($rt / $total) * 100, 1) : null;
                                        $ach = $achValue !== null ? $achValue.'%' : '-';
                                        $info = $row['INFORMATION'] ?? '';

                                        $totals[$date] = ($totals[$date] ?? 0) + $total;
                                        $realtimes[$date] = ($realtimes[$date] ?? 0) + $rt;

                                        $hourInt = (int) explode(':', $hour)[0];
                                        if ($hourInt >= 7 && $hourInt < 19) { $siangTotals[$date]=($siangTotals[$date]
                                            ?? 0) + $total; $siangRealtimes[$date]=($siangRealtimes[$date] ?? 0) + $rt;
                                            } else { $malamTotals[$date]=($malamTotals[$date] ?? 0) + $total;
                                            $malamRealtimes[$date]=($malamRealtimes[$date] ?? 0) + $rt; }
                                            $isLowAch=$achValue !==null && $achValue < 95; $rowTotalAllDates +=$total;
                                            $rowRealtimeAllDates +=$rt; $achValueAllDates=($rowTotalAllDates> 0) ?
                                            round(($rowRealtimeAllDates / $rowTotalAllDates) * 100, 1) : null;
                                            $rowAchAllDates = $achValueAllDates !== null ? $achValueAllDates.'%' : '-';

                                            $isLowAchrowTotal = $achValueAllDates !== null && $achValueAllDates < 95;
                                                @endphp <td @if($isLowAch) style="background-color: #e77f88;" @endif>
                                                {{ $total ?: '' }}</td>
                                                <td @if($isLowAch) style="background-color: #e77f88;" @endif>
                                                    {{ $rt ?: '' }}</td>
                                                <td @if($isLowAch) style="background-color: #e77f88;" @endif>{{ $ach }}
                                                </td>
                                                <td style="text-align: left">{{ $info }}</td>
                                                @endforeach
                                                <td @if($isLowAchrowTotal) style="background-color: #e77f88;" @endif>
                                                    {{ $rowTotalAllDates }}</td>
                                                <td @if($isLowAchrowTotal) style="background-color: #e77f88;" @endif>
                                                    {{ $rowRealtimeAllDates }}</td>
                                                <td @if($isLowAchrowTotal) style="background-color: #e77f88;" @endif>
                                                    {{ $rowAchAllDates }}</td>
                                    </tr>
                                    @endforeach
                            </tbody>
                            <tfoot>
                                {{-- Total --}}
                                <tr class="table-success fw-bold">
                                    <td class="sticky-col">Total</td>
                                    @php
                                    $grandTotalAllDates = 0;
                                    $grandRealtimeAllDates = 0;
                                    @endphp
                                    @foreach($groupedRitation as $date => $records)
                                    @php
                                    $achValue = ($totals[$date] > 0) ? round(($realtimes[$date] / $totals[$date]) * 100,
                                    1) : null;
                                    $ach = $achValue !== null ? $achValue.'%' : '-';
                                    $isLowAch = $achValue !== null && $achValue < 95; $grandTotalAllDates
                                        +=$totals[$date]; $grandRealtimeAllDates +=$realtimes[$date]; @endphp <td
                                        @if($isLowAch) style="background-color: #e77f88;" @endif>{{ $totals[$date] }}
                                        </td>
                                        <td @if($isLowAch) style="background-color: #e77f88;" @endif>
                                            {{ $realtimes[$date] }}</td>
                                        <td @if($isLowAch) style="background-color: #e77f88;" @endif>{{ $ach }}</td>
                                        <td></td>
                                        @endforeach
                                        @php
                                        $grandAchValue = ($grandTotalAllDates > 0) ? round(($grandRealtimeAllDates /
                                        $grandTotalAllDates) * 100, 1) : null;
                                        $grandAch = $grandAchValue !== null ? $grandAchValue.'%' : '-';
                                        $isGrandLowAch = $grandAchValue !== null && $grandAchValue < 95; @endphp <td
                                            class="table-success @if($isGrandLowAch) text-danger @endif">
                                            {{ $grandTotalAllDates }}</td>
                                            <td class="table-success @if($isGrandLowAch) text-danger @endif">
                                                {{ $grandRealtimeAllDates }}</td>
                                            <td class="table-success @if($isGrandLowAch) text-danger @endif">
                                                {{ $grandAch }}</td>
                                </tr>

                                {{-- Selisih --}}
                                <tr class="fw-bold">
                                    <td class="sticky-col">Selisih</td>
                                    @php $grandSelisihAllDates = 0; @endphp
                                    @foreach($groupedRitation as $date => $records)
                                    @php
                                    $selisih = $totals[$date] - $realtimes[$date];
                                    $grandSelisihAllDates += $selisih;
                                    @endphp
                                    <td colspan="4" class="text-start">{{ $selisih }}</td>
                                    @endforeach
                                    <td class="bg-warning" colspan="3">{{ $grandSelisihAllDates }}</td>
                                </tr>

                                {{-- Siang --}}
                                <tr class="table-warning fw-bold">
                                    <td class="sticky-col">Siang</td>
                                    @php
                                    $grandSiangAllDates = 0;
                                    $grandSiangRealtimeAllDates = 0;
                                    @endphp
                                    @foreach($groupedRitation as $date => $records)
                                    @php
                                    $achValue = ($siangTotals[$date] > 0) ? round(($siangRealtimes[$date] /
                                    $siangTotals[$date]) * 100, 1) : null;
                                    $ach = $achValue !== null ? $achValue.'%' : '-';

                                    $grandSiangAllDates += $siangTotals[$date] ?? 0;
                                    $grandSiangRealtimeAllDates += $siangRealtimes[$date] ?? 0;
                                    @endphp
                                    <td>{{ $siangTotals[$date] ?? '' }}</td>
                                    <td>{{ $siangRealtimes[$date] ?? '' }}</td>
                                    <td>{{ $ach }}</td>
                                    <td></td>
                                    @endforeach
                                    @php
                                    $grandSiangAchValue = ($grandSiangAllDates > 0) ? round(($grandSiangRealtimeAllDates
                                    / $grandSiangAllDates) * 100, 1) : null;
                                    $grandSiangAch = $grandSiangAchValue !== null ? $grandSiangAchValue.'%' : '-';
                                    @endphp
                                    <td class="table-warning">{{ $grandSiangAllDates }}</td>
                                    <td class="table-warning">{{ $grandSiangRealtimeAllDates }}</td>
                                    <td class="table-warning">{{ $grandSiangAch }}</td>
                                </tr>

                                {{-- Malam --}}
                                <tr class="table-dark fw-bold">
                                    <td class="sticky-col">Malam</td>
                                    @php
                                    $grandMalamAllDates = 0;
                                    $grandMalamRealtimeAllDates = 0;
                                    @endphp
                                    @foreach($groupedRitation as $date => $records)
                                    @php
                                    $achValue = ($malamTotals[$date] > 0) ? round(($malamRealtimes[$date] /
                                    $malamTotals[$date]) * 100, 1) : null;
                                    $ach = $achValue !== null ? $achValue.'%' : '-';

                                    $grandMalamAllDates += $malamTotals[$date] ?? 0;
                                    $grandMalamRealtimeAllDates += $malamRealtimes[$date] ?? 0;
                                    @endphp
                                    <td>{{ $malamTotals[$date] ?? '' }}</td>
                                    <td>{{ $malamRealtimes[$date] ?? '' }}</td>
                                    <td>{{ $ach }}</td>
                                    <td></td>
                                    @endforeach
                                    @php
                                    $grandMalamAchValue = ($grandMalamAllDates > 0) ? round(($grandMalamRealtimeAllDates
                                    / $grandMalamAllDates) * 100, 1) : null;
                                    $grandMalamAch = $grandMalamAchValue !== null ? $grandMalamAchValue.'%' : '-';
                                    @endphp
                                    <td class="table-dark">{{ $grandMalamAllDates }}</td>
                                    <td class="table-dark">{{ $grandMalamRealtimeAllDates }}</td>
                                    <td class="table-dark">{{ $grandMalamAch }}</td>
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

@include('layout.footer')

