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

class SummaryMaintenanceUnitExport implements FromCollection, WithEvents, WithStyles, WithDrawings, Responsable, WithTitle
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    private $data;
    private $fileName = 'Unit_Maintenance_FMS_Report.xlsx';

    public function __construct(Collection $data, $date)
    {
        $this->data = $data;
        $this->date = $date;

        $this->vehicleGroups = [
                    'Hauler' => [
                        'HD' => 'Heavy Duty',
                    ],
                    'Excavator' => [
                        'EX' => 'Excavator',
                    ],
                    'Unit Support' => [
                        'MG' => 'Motorgrader',
                        'BD' => 'Bulldozer',
                        'WT' => 'Water Tank',
                    ],
                ];
    }

    public function collection()
    {
        return collect([]);
    }

    public function title(): string
    {
        return 'Unit Maintenance Monthly';
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

                // Judul utama
                $sheet->mergeCells('A1:I1');
                $sheet->setCellValue('A1', 'Unit Maintenance Monthly Summary');
                $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                // Group data berdasarkan prefix NAME
                $groupedData = collect($this->data)->groupBy(function ($item) {
                    return strtoupper(substr($item->NAME, 0, 2));
                });

                $currentRow = 3;

                foreach ($groupedData as $code => $items) {
                    $currentRow++;

                    // Judul grup
                    $sheet->mergeCells("A{$currentRow}:I{$currentRow}");
                    // Mapping kode ke nama unit
                    $unitNames = [
                        'BD' => 'BULL DOZER',
                        'EX' => 'EXCAVATOR',
                        'MG' => 'MOTOR GRADER',
                        'HD' => 'HEAVY DUTY',
                        'WT' => 'WATER TRUCK',
                    ];
                    $unitName = $unitNames[$code] ?? $code;

                    // Tulis judul
                    $sheet->setCellValue("A{$currentRow}", "{$unitName}");
                    $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true)->setSize(12);
                    $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal('center');
                    $currentRow++;

                    $startBorderRow = $currentRow;
                    $headers = ['No', 'Name', 'Code', 'Lokasi', 'Status', 'Terakhir Maintenance', 'Remarks', 'On-site', 'Reporting'];
                    foreach (range('A', 'I') as $index => $col) {
                        $sheet->setCellValue("{$col}{$currentRow}", $headers[$index]);
                    }
                    $sheet->getStyle("A{$currentRow}:I{$currentRow}")->getFont()->setBold(true);
                    $sheet->getStyle("A{$currentRow}:I{$currentRow}")->getAlignment()->setHorizontal('center');
                    $sheet->getStyle("A{$currentRow}:I{$currentRow}")->getFill()
                        ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');
                    $currentRow++;

                    // Data tabel
                    $no = 1;
                    foreach ($items as $item) {
                        $sheet->setCellValue("A{$currentRow}", $no++);
                        $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal('center');
                        $sheet->setCellValue("B{$currentRow}", $item->NAME);
                        $sheet->setCellValue("C{$currentRow}", $item->CODE);
                        $sheet->setCellValue("D{$currentRow}", $item->LOCATION);
                        $sheet->setCellValue("E{$currentRow}", $item->STATUS);
                        $sheet->setCellValue("F{$currentRow}", $item->LAST_MAINTAINED);
                        $sheet->setCellValue("G{$currentRow}", $item->REMARKS);
                        $sheet->setCellValue("H{$currentRow}", $item->ACTION_BY);
                        $sheet->setCellValue("I{$currentRow}", $item->REPORTING);

                        // Warna jika sudah maintenance
                        if (strtolower($item->STATUS) === 'already maintained') {
                            $sheet->getStyle("A{$currentRow}:I{$currentRow}")->getFill()
                                ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('C6EFCE');
                        }
                        $currentRow++;


                    }

                }

                // Auto-size semua kolom A–I
                foreach (range('A', 'I') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Border data
                $startRow = 4;
                $endRow = $startRow + count($this->data) + 20;
                $sheet->getStyle("A{$startRow}:I{$endRow}")->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                /**
                 * DASHBOARD AREA
                 */

                $dashboardColStart = 'K';
                $dashboardRowStart = 4;

                // Judul
                $sheet->setCellValue("{$dashboardColStart}{$dashboardRowStart}", '◌ Unit Dashboard');
                $sheet->getStyle("{$dashboardColStart}{$dashboardRowStart}")->getFont()->setItalic(true);

                // Header
                $headers2 = [
                    'Vehicle Group', 'Vehicle Type', 'Plan', 'Actual', 'Ach',
                    'Total Maintained', 'Ready for Maintenance',
                    "Today's Maintained (7per Day)", 'Daily Percentage', 'Monthly Percentage'
                ];
                $cols2 = range('K', 'T');

                foreach ($headers2 as $i => $header) {
                    $col = $cols2[$i];
                    $sheet->setCellValue("{$col}" . ($dashboardRowStart + 1), $header);
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                    $sheet->getStyle("{$col}" . ($dashboardRowStart + 1))->getAlignment()->setHorizontal('center');
                    $sheet->getStyle("{$col}" . ($dashboardRowStart + 1))->getFont()->setBold(true);
                    $sheet->getStyle("{$col}" . ($dashboardRowStart + 1))->getFill()
                        ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');
                }

                // Data
                $row = $dashboardRowStart + 2;
                $today = $this->date;

                foreach ($this->vehicleGroups as $group => $vehicles) {
                    foreach ($vehicles as $code => $type) {
                        $filteredUnitPlan = collect($this->data)->filter(function ($item) use ($code) {
                            return strtoupper(substr($item->NAME, 0, 2)) === strtoupper($code);
                        })->count();

                        $filteredUnitActual = collect($this->data)->filter(function ($unit) use ($code) {
                            return strtoupper(substr($unit->NAME, 0, 2)) === strtoupper($code) && $unit->STATUS === 'Already Maintained';
                        })->count();

                        $ach = $filteredUnitPlan > 0 ? round(($filteredUnitActual / $filteredUnitPlan) * 100) . '%' : '0%';



                        $sheet->setCellValue("K{$row}", $group);
                        $sheet->setCellValue("L{$row}", $type);
                        $sheet->setCellValue("M{$row}", $filteredUnitPlan);
                        $sheet->setCellValue("N{$row}", $filteredUnitActual);
                        $sheet->setCellValue("O{$row}", $ach);

                        $sheet->getStyle("M{$row}")->getAlignment()->setHorizontal('center')->setVertical('center');
                        $sheet->getStyle("N{$row}")->getAlignment()->setHorizontal('center')->setVertical('center');
                        $sheet->getStyle("O{$row}")->getAlignment()->setHorizontal('center')->setVertical('center');

                        $row++;
                    }
                }

                $data = collect($this->data);
                $totalPlanAll = $data->count();
                $totalMaintainedAll = $data->where('STATUS', 'Already Maintained')->count();
                $readyForMaintenance = $data->where('STATUS', 'Ready For Maintenance')->count();

                $todayMaintained = $data->filter(fn($item) =>
                    $item->STATUS === 'Already Maintained' &&
                    \Carbon\Carbon::parse($item->LAST_MAINTAINED)->format('Y-m-d') === $today
                )->count();

                // Hitung persentase
                $percentage = fn($value, $total) => $total > 0 ? round(($value / $total) * 100) . '%' : '0%';
                $totalMaintainedPercent = $totalMaintainedAll;
                $readyForMaintenancePercent = $readyForMaintenance;
                $todayMaintainedPercent = $percentage($totalMaintainedAll, $totalPlanAll);
                $dailyPercent = $percentage($totalMaintainedAll, 7);
                $monthlyPercent = $percentage($totalMaintainedAll, $totalPlanAll);

                $columns = ['P' => $totalMaintainedPercent, 'Q' => $readyForMaintenancePercent, 'R' => $todayMaintainedPercent, 'S' => $dailyPercent, 'T' => $monthlyPercent];
                $rowStart = $dashboardRowStart + 2;
                $rowEnd = $row - 1;

                foreach ($columns as $col => $value) {
                    $sheet->mergeCells("{$col}{$rowStart}:{$col}{$rowEnd}");
                    $sheet->setCellValue("{$col}{$rowStart}", $value);
                    $sheet->getStyle("{$col}{$rowStart}")->getAlignment()->setHorizontal('center')->setVertical('center');
                }


                // Border
                $sheet->getStyle("K" . ($dashboardRowStart + 1) . ":T" . ($row - 1))->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }

}
