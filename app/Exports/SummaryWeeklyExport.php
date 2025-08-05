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
use Carbon\Carbon;

class SummaryWeeklyExport implements FromCollection, WithEvents, WithStyles, WithDrawings, Responsable, WithTitle
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    private $monthlyActivity;
    private $fileName = 'weekly_activity.xlsx';
    private $date;
    private $weekly;
    private $startDate;
    private $endDateMingguDepan;

    public function __construct(Collection $monthlyActivity, $weekly, $date = null, $startDate, $endDateMingguDepan)
    {
        $this->monthlyActivity = $monthlyActivity;
        $this->weekly = $weekly;
        $this->startDate = $startDate;
        $this->endDateMingguDepan = $endDateMingguDepan;
        $this->date = $date ?? now()->format('Y-m-d');
    }

    public function collection()
    {
        return collect([]);
    }
    public function title(): string
    {
        return 'Weekly Activity';
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

                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', 'Weekly Activity');
                $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                $sheet->mergeCells('A2:G2');
                $sheet->setCellValue('A2', 'Periode : ' . Carbon::parse($this->startDate)->translatedFormat('d F Y') . ' - ' . Carbon::parse($this->endDateMingguDepan)->translatedFormat('d F Y'));
                $sheet->getStyle('A2')->getFont()->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

                $sheet->mergeCells('A3:G3');
                $sheet->setCellValue('A3', 'Departemen: GA - IT');
                $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(10);

                // ===== Header Tabel =====
                $startRow = 5;

                // Baris pertama header (row 5)
                $sheet->setCellValue("A{$startRow}", 'No');
                $sheet->setCellValue("B{$startRow}", 'Section');
                $sheet->setCellValue("C{$startRow}", 'Team');
                $sheet->mergeCells("D{$startRow}:E{$startRow}");
                $sheet->setCellValue("D{$startRow}", 'Activity (Work Items & PIC)');
                $sheet->mergeCells("F{$startRow}:G{$startRow}");
                $sheet->setCellValue("F{$startRow}", 'Plan (Work Items & PIC)');


                // Baris kedua header (row 6)
                $sheet->setCellValue("D" . ($startRow + 1), 'Work Items');
                $sheet->setCellValue("E" . ($startRow + 1), 'PIC');
                $sheet->setCellValue("F" . ($startRow + 1), 'Work Items');
                $sheet->setCellValue("G" . ($startRow + 1), 'PIC');

                // Merge kolom No, Team, Reporting ke bawah untuk baris 2 header
                $sheet->mergeCells("A{$startRow}:A" . ($startRow + 1));
                $sheet->mergeCells("B{$startRow}:B" . ($startRow + 1));
                $sheet->mergeCells("C{$startRow}:C" . ($startRow + 1));

                // Styling header
                $headerRange = "A{$startRow}:G" . ($startRow + 1);
                $sheet->getStyle($headerRange)->getFont()->setBold(true);
                $sheet->getStyle($headerRange)->getAlignment()->setHorizontal('center')->setVertical('center');
                $sheet->getStyle($headerRange)->getFill()
                    ->setFillType('solid')
                    ->getStartColor()->setRGB('D9E1F2');

                // Isi data dimulai dari baris ke-7
                $row = $startRow + 2;
                $no = 1;
                $monthly = collect($this->monthlyActivity);
                $teams = $monthly->pluck('TEAM')->merge($this->monthlyActivity->pluck('TEAM'))->unique()->values();
                $weekly = collect($this->weekly);
                $grouped = collect();
                        foreach ($teams as $team) {
                            $activities = $monthly->where('TEAM', $team)->values();
                            $plans = $weekly->where('TEAM', $team)->values();

                            $max = max($activities->count(), $plans->count());

                            for ($i = 0; $i < $max; $i++) {
                                $grouped->push([
                                    'TEAM' => $team,
                                    'ACTIVITY' => $activities[$i]->ACTIVITY ?? null,
                                    'PIC_ACTIVITY' => $activities[$i]->PIC ?? null,
                                    'DATE_REPORT' => $activities[$i]->DATE_REPORT ?? null,
                                    'PLAN' => $plans[$i]->WORK_ITEMS ?? null,
                                    'PIC_PLAN' => $plans[$i]->ACTION_BY ?? null,
                                ]);
                            }
                        }

                $groupedByTeam = $grouped->groupBy('TEAM');
                $yellowColor = 'FFFACD';
                $highlightTime = Carbon::parse('16:30');

                foreach ($groupedByTeam as $team => $activities) {
                    $teamStartRow = $row;

                    foreach ($activities as $daily) {
                        $sheet->setCellValue("A{$row}", $no++);
                        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal('center');

                        $sheet->setCellValue("B{$row}", 'IT');
                        $sheet->setCellValue("C{$row}", $team);

                        // Weekly Plan
                        $sheet->setCellValue("D{$row}", '(' . Carbon::parse($daily['DATE_REPORT'])->translatedFormat('d F Y') . ') ' . $daily['ACTIVITY']);
                        $sheet->setCellValue("E{$row}", $daily['PIC_ACTIVITY']);

                        // Monthly Plan
                        $sheet->setCellValue("F{$row}", $daily['PLAN']);
                        $sheet->setCellValue("G{$row}", $daily['PIC_PLAN']);

                        // Highlight baris jika waktu laporan lebih dari jam 16:30
                        $reportDate = Carbon::parse($daily['DATE_REPORT']);
                        if ($reportDate->format('H:i') > $highlightTime->format('H:i')) {
                            $sheet->getStyle("A{$row}:G{$row}")->getFill()
                                ->setFillType('solid')
                                ->getStartColor()->setRGB($yellowColor);
                        }

                        $row++;
                    }

                    // Merge kolom Section
                    if ($teamStartRow !== ($row - 1)) {
                        $sheet->mergeCells("B{$teamStartRow}:B" . ($row - 1));
                        $sheet->getStyle("B{$teamStartRow}:B" . ($row - 1))->getAlignment()->setVertical('center')->setHorizontal('center');
                    }

                    // Merge kolom Team
                    if ($teamStartRow !== ($row - 1)) {
                        $sheet->mergeCells("C{$teamStartRow}:C" . ($row - 1));
                        $sheet->getStyle("C{$teamStartRow}:C" . ($row - 1))->getAlignment()->setVertical('center')->setHorizontal('center');
                    }
                }

                // Footer keterangan
                $lastRow = $row - 1;
                $sheet->mergeCells("A" . ($lastRow + 2) . ":G" . ($lastRow + 2));
                $sheet->setCellValue("A" . ($lastRow + 2), 'Keterangan: Warna kuning pekerjaan di atas jam 16.30');
                $sheet->getStyle("A" . ($lastRow + 2))->getFont()->setItalic(true);

                // Border seluruh tabel
                $sheet->getStyle("A{$startRow}:G{$lastRow}")
                    ->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // Auto width
                foreach (range('A', 'G') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

            },
        ];
    }
}
