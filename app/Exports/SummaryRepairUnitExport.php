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

class SummaryRepairUnitExport implements FromCollection, WithEvents, WithStyles, WithDrawings, Responsable, WithTitle
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    private $data;
    private $fileName = 'Unit_Repair_FMS_Report.xlsx';

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect([]);
    }

    public function title(): string
    {
        return 'Unit Repair Monthly';
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
                $sheet->mergeCells('A1:J1');
                $sheet->setCellValue('A1', 'Unit Repair Monthly Summary');
                $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                // Table headers
                $startRow = 3;
                $headers = ['No', 'Type', 'Name', 'Location', 'Date Action', 'Problem', 'Action', 'Remarks', 'On-site', 'Reporting'];
                $col = 'A';
                foreach ($headers as $header) {
                    $sheet->setCellValue("{$col}{$startRow}", $header);
                    $col++;
                }

                $sheet->getStyle("A{$startRow}:J{$startRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$startRow}:J{$startRow}")->getAlignment()->setHorizontal('center');
                $sheet->getStyle("A{$startRow}:J{$startRow}")->getFill()->setFillType('solid')->getStartColor()->setRGB('D9E1F2');

                // Table data
                $row = $startRow + 1;

                foreach ($this->data as $index => $item) {
                    // Zebra color: jika genap → putih, jika ganjil → abu muda
                    $fillColor = ($index % 2 == 0) ? 'FFFFFF' : 'F2F2F2';
                    $range = "A{$row}:J{$row}";

                    // Isi data per kolom
                    $sheet->setCellValue("A{$row}", $index + 1);
                    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal('center');
                    $sheet->setCellValue("C{$row}", $item->TYPE_DESC);
                    $sheet->setCellValue("B{$row}", $item->NAMA_UNIT);
                    $sheet->setCellValue("D{$row}", $item->LOKASI);
                    $sheet->setCellValue("E{$row}", $item->DATE_ACTION
                        ? \Carbon\Carbon::parse($item->DATE_ACTION)->translatedFormat('d F Y')
                        : '');
                    $sheet->setCellValue("F{$row}", $item->ACTUAL_PROBLEM);
                    $sheet->setCellValue("G{$row}", $item->ACTION_PROBLEM);
                    $sheet->setCellValue("H{$row}", $item->REMARKS);
                    $sheet->setCellValue("I{$row}", $item->ACTION_BY);
                    $sheet->setCellValue("J{$row}", $item->REPORTING);

                    // Apply zebra background
                    $sheet->getStyle($range)->getFill()->setFillType('solid')->getStartColor()->setRGB($fillColor);

                    $row++;
                }

                // Auto size
                foreach (range('A', 'J') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Border
                $sheet->getStyle("A{$startRow}:J" . ($row - 1))->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }

}
