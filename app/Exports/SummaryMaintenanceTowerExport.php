<?php
namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class SummaryMaintenanceTowerExport implements FromCollection, WithEvents, WithStyles, WithDrawings, Responsable, WithTitle
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    private $data;
    private $fileName = 'Tower_Maintenance_FMS_Report.xlsx';

    public function __construct(Collection $data, $date)
    {
        $this->data = $data;
        $this->date = $date;
    }

    public function collection()
    {
        return collect([]);
    }

    public function title(): string
    {
        return 'Tower Maintenance Monthly';
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Company Logo');
        $drawing->setPath(public_path('dashboard/assets/images/sims.png'));
        $drawing->setHeight(40);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(10);
        return [$drawing];
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Header
                $sheet->mergeCells('A1:I1');
                $sheet->setCellValue('A1', 'Tower Maintenance Monthly Summary');
                $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                // Table headers
                $startRow = 3;
                $headers = ['No', 'Kode', 'Nama', 'Lokasi', 'Status', 'Terakhir Maintenance', 'Remarks', 'On-site', 'Reporting'];
                $col = 'A';
                foreach ($headers as $header) {
                    $sheet->setCellValue("{$col}{$startRow}", $header);
                    $col++;
                }

                $sheet->getStyle("A{$startRow}:I{$startRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$startRow}:I{$startRow}")->getAlignment()->setHorizontal('center');
                $sheet->getStyle("A{$startRow}:I{$startRow}")->getFill()->setFillType('solid')->getStartColor()->setRGB('D9E1F2');

                // Table data
                $row = $startRow + 1;
                $summary = ['TL' => 0, 'MT' => 0, 'FTW' => 0];
                $total = ['TL' => 0, 'MT' => 0, 'FTW' => 0];

                foreach ($this->data as $index => $item) {
                    $sheet->setCellValue("A{$row}", $index + 1);
                    $sheet->getStyle("A{$row}", $index + 1)->getAlignment()->setHorizontal('center');
                    $sheet->setCellValue("B{$row}", $item->CODE);
                    $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal('center');
                    $sheet->setCellValue("C{$row}", $item->NAME);
                    $sheet->setCellValue("D{$row}", $item->LOCATION);
                    $sheet->setCellValue("E{$row}", $item->STATUS);
                    $sheet->setCellValue("F{$row}", $item->LAST_MAINTAINED
                        ? \Carbon\Carbon::parse($item->LAST_MAINTAINED)->translatedFormat('d F Y H:i')
                        : ''
                );
                    $sheet->setCellValue("G{$row}", $item->REMARKS);
                    $sheet->setCellValue("H{$row}", $item->ACTION_BY);
                    $sheet->setCellValue("I{$row}", $item->REPORTING);

                    // Highlight status Already Maintained
                    if (strtolower($item->STATUS) === 'already maintained') {
                        $sheet->getStyle("A{$row}:I{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('C6EFCE');
                    }

                    // Count summary
                    if (isset($summary[$item->CODE])) {
                        $total[$item->CODE]++;
                        if (strtolower($item->STATUS) === 'already maintained') {
                            $summary[$item->CODE]++;
                        }
                    }

                    $row++;
                }

                foreach (range('A', 'I') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $sheet->getStyle("A{$startRow}:I" . ($row - 1))->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    $dashboardColStart = 'K';
                    $dashboardRowStart = 3;

                    $sheet->setCellValue("{$dashboardColStart}{$dashboardRowStart}", '◌ Tower Dashboard');
                    $sheet->getStyle("{$dashboardColStart}{$dashboardRowStart}")->getFont()->setItalic(true);

                    $headers2 = [
                        'Tower Type', 'Plan', 'Actual', 'Ach',
                        'Total Maintained', 'Ready For Maintenance', 'Today Maintained', 'Monthly Percentage'
                    ];
                    $cols2 = ['K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R'];

                    foreach ($headers2 as $i => $header) {
                        $col = $cols2[$i];
                        $sheet->setCellValue("{$col}" . ($dashboardRowStart + 2), $header);
                        $sheet->getColumnDimension($col)->setAutoSize(true);
                        $sheet->getStyle($col)->getAlignment()->setHorizontal('center');
                    }

                    $types = [
                        'TL' => 'Tower Lamp',
                        'MT' => 'Mobile Tower',
                        'FTW' => 'Fix Tower',
                    ];

                    $row2 = $dashboardRowStart + 3;
                    $today = $this->date;
                    $currentMonth = now()->format('Y-m');
                    $rowStart = $row2;
                    foreach ($types as $code => $label) {
                        $plan = $total[$code];
                        $actual = $summary[$code];
                        $ach = $plan > 0 ? round(($actual / $plan) * 100) . '%' : '0%';

                        $filtered = collect($this->data)->where('CODE', $code);

                        $totalMaintained = $filtered->where('STATUS', 'Already Maintained')->count();

                        $readyForMaintenance = $filtered->where('STATUS', 'Ready For Maintenance')->count();

                        $todayMaintained = $filtered->filter(function ($item) use ($today) {
                            return $item->STATUS == 'Already Maintained' && Carbon::parse($item->LAST_MAINTAINED)->toDateString() == $today;
                        })->count();

                        // Set data ke sheet
                        $sheet->setCellValue("K{$row2}", $label);
                        $sheet->setCellValue("L{$row2}", $plan);
                        $sheet->setCellValue("M{$row2}", $actual);
                        $sheet->setCellValue("N{$row2}", $ach);
                        $sheet->setCellValue("O{$row2}", $totalMaintained);
                        $sheet->setCellValue("P{$row2}", $readyForMaintenance);
                        $sheet->setCellValue("Q{$row2}", $todayMaintained);

                        $row2++;
                    }

                    $rowEnd = $row2 - 1;

                    $totalPlanAll = array_sum($total);
                    $totalMaintainedAll = collect($this->data)->where('STATUS', 'Already Maintained')->count();
                    $monthlyPercentage = $totalPlanAll > 0 ? round(($totalMaintainedAll / $totalPlanAll) * 100) . '%' : '0%';


                    $sheet->mergeCells("R{$rowStart}:R{$rowEnd}");
                    $sheet->setCellValue("R{$rowStart}", $monthlyPercentage);
                    $sheet->getStyle("R{$rowStart}")->getAlignment()->setHorizontal('center');
                    $sheet->getStyle("R{$rowStart}")->getAlignment()->setVertical('center');

                    $sheet->getStyle("K" . ($dashboardRowStart + 2) . ":R" . ($row2 - 1))->getBorders()->getAllBorders()
                        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $sheet->getStyle("K" . ($dashboardRowStart + 2) . ":R" . ($dashboardRowStart + 2))->getFont()->setBold(true);
                    $sheet->getStyle("K" . ($dashboardRowStart + 2) . ":R" . ($dashboardRowStart + 2))->getFill()
                        ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');

            },
        ];
    }
}
