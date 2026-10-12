<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class DailyActivityExport implements FromCollection, WithEvents, WithStyles, WithDrawings, Responsable, WithTitle
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    private $dailyActivity;
    private $fileName = 'daily_activity.xlsx';
    private $date;

    public function __construct(Collection $dailyActivity, $date = null)
    {
        $this->dailyActivity = $dailyActivity;
        $this->date = $date ?? now()->format('Y-m-d');
    }

    public function collection()
    {
        return collect([]);
    }

    public function title(): string
    {
        return 'Daily Activity';
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
        $drawing->setOffsetY(10);

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
                $dateFormatted = Carbon::parse($this->date)->translatedFormat('d F Y');

                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'Daily Activity');
                $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                $sheet->mergeCells('A2:H2');
                $sheet->setCellValue('A2', 'Tanggal: ' . $dateFormatted);
                $sheet->getStyle('A2')->getFont()->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

                $sheet->mergeCells('A3:H3');
                $sheet->setCellValue('A3', 'Departemen: GA - IT');
                $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(10);

                $startRow = 5;

                $sheet->setCellValue("A{$startRow}", 'No');
                $sheet->setCellValue("B{$startRow}", 'Team');
                $sheet->setCellValue("C{$startRow}", 'Jam Action');
                $sheet->setCellValue("D{$startRow}", 'Jam Finish');
                $sheet->setCellValue("E{$startRow}", 'Status');
                $sheet->setCellValue("F{$startRow}", 'Activity');
                $sheet->setCellValue("G{$startRow}", 'On-site');
                $sheet->setCellValue("H{$startRow}", 'Reporting');

                $sheet->getStyle("A{$startRow}:H{$startRow}")
                    ->getFont()
                    ->setBold(true);

                $sheet->getStyle("A{$startRow}:H{$startRow}")
                    ->getAlignment()
                    ->setHorizontal('center');

                $sheet->getStyle("A{$startRow}:H{$startRow}")
                    ->getFill()
                    ->setFillType('solid')
                    ->getStartColor()
                    ->setRGB('D9E1F2');

                $row = $startRow + 1;
                $no = 1;

                $grouped = $this->dailyActivity->groupBy('TEAM');
                $yellowColor = 'FFFACD';
                $highlightTime = Carbon::parse('16:30');

                foreach ($grouped as $team => $activities) {
                    $teamStartRow = $row;

                    foreach ($activities as $daily) {
                        $sheet->setCellValue("A{$row}", $no++);
                        $sheet->setCellValue("B{$row}", $team);

                        $startTime = Carbon::parse($daily->START);
                        $finishTime = Carbon::parse($daily->FINISH);

                        $sheet->setCellValue("C{$row}", $startTime->format('H:i'));
                        $sheet->setCellValue("D{$row}", $finishTime->format('H:i'));
                        $sheet->setCellValue("E{$row}", $daily->NAMA_STATUS ?? '');
                        $sheet->setCellValue("F{$row}", $daily->ACTIVITY);
                        $sheet->setCellValue("G{$row}", $daily->PIC);
                        $sheet->setCellValue("H{$row}", $daily->REPORTING);

                        if ($finishTime->gt($highlightTime)) {
                            $sheet->getStyle("A{$row}:H{$row}")
                                ->getFill()
                                ->setFillType('solid')
                                ->getStartColor()
                                ->setRGB($yellowColor);
                        }

                        $sheet->getStyle("A{$row}")
                            ->getAlignment()
                            ->setHorizontal('center');

                        $sheet->getStyle("C{$row}:E{$row}")
                            ->getAlignment()
                            ->setHorizontal('center');

                        $row++;
                    }

                    if ($teamStartRow !== ($row - 1)) {
                        $sheet->mergeCells("B{$teamStartRow}:B" . ($row - 1));

                        $sheet->getStyle("B{$teamStartRow}:B" . ($row - 1))
                            ->getAlignment()
                            ->setVertical('center');
                    }
                }

                $lastRow = $row - 1;

                $sheet->mergeCells(
                    "A" . ($lastRow + 2) . ":H" . ($lastRow + 2)
                );

                $sheet->setCellValue(
                    "A" . ($lastRow + 2),
                    'Keterangan: Warna kuning pekerjaan di atas jam 16.30'
                );

                $sheet->getStyle("A" . ($lastRow + 2))
                    ->getFont()
                    ->setItalic(true);

                $sheet->getStyle("A{$startRow}:H{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                foreach (range('A', 'H') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
