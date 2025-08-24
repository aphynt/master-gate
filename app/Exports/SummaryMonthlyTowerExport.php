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

class SummaryMonthlyTowerExport implements FromCollection, WithEvents, WithStyles, WithDrawings, Responsable, WithTitle
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    private $towerMonthlyActivity;
    private $nonMtStart;
    private $fileName = 'Tower_Monthly_FMS_Report.xlsx';

    public function __construct(Collection $towerMonthlyActivity, $nonMtStart)
    {
        $this->towerMonthlyActivity = $towerMonthlyActivity;
        $this->nonMtStart = $nonMtStart;
    }

    public function collection()
    {
        return collect([]);
    }

    public function title(): string
    {
        return 'Tower';
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
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'Laporan Aktivitas Tower FMS (Network) Bulan '. \Carbon\Carbon::parse($this->nonMtStart)->translatedFormat('F Y') );
                $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                // Table headers
                $startRow = 3;
                $headers = ['No', 'Tanggal', 'No Tower', 'Deskripsi Problem', 'Action Problem', 'Remarks', 'On-site', 'Reporting'];
                $col = 'A';
                foreach ($headers as $header) {
                    $sheet->setCellValue("{$col}{$startRow}", $header);
                    $col++;
                }

                $sheet->getStyle("A{$startRow}:H{$startRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$startRow}:H{$startRow}")->getAlignment()->setHorizontal('center');
                $sheet->getStyle("A{$startRow}:H{$startRow}")->getFill()->setFillType('solid')->getStartColor()->setRGB('D9E1F2');

                // Table data
                $row = $startRow + 1;

                foreach ($this->towerMonthlyActivity as $index => $item) {
                    // Zebra color: jika genap → putih, jika ganjil → abu muda
                    $fillColor = ($index % 2 == 0) ? 'FFFFFF' : 'F2F2F2';
                    $range = "A{$row}:H{$row}";

                    // Isi data per kolom
                    $sheet->setCellValue("A{$row}", $index + 1);
                    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal('center');
                    $sheet->setCellValue("B{$row}", $item->DATE_REPORT
                        ? \Carbon\Carbon::parse($item->DATE_REPORT)->translatedFormat('d F Y')
                        : '');
                    $sheet->setCellValue("C{$row}", $item->NAMA_ITEM);
                    $sheet->setCellValue("D{$row}", $item->ACTUAL_PROBLEM);
                    $sheet->setCellValue("E{$row}", $item->ACTION_PROBLEM);
                    $sheet->setCellValue("F{$row}", $item->REMARKS);
                    $sheet->setCellValue("G{$row}", $item->PIC);
                    $sheet->setCellValue("H{$row}", $item->REPORTING);

                    // Apply zebra background
                    $sheet->getStyle($range)->getFill()->setFillType('solid')->getStartColor()->setRGB($fillColor);

                    $row++;
                }

                // Auto size
                foreach (range('A', 'H') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                    $sheet->getStyle($col)->getAlignment()->setShrinkToFit(true);
                }

                // Border
                $sheet->getStyle("A{$startRow}:H" . ($row - 1))->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }

}
