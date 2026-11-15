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
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ListUnitExport implements
    FromCollection,
    WithEvents,
    WithStyles,
    WithDrawings,
    Responsable,
    WithTitle
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    private $listUnit;

    public function __construct(Collection $listUnit)
    {
        $this->listUnit = $listUnit;
    }

    public function collection()
    {
        return collect([]);
    }

    public function title(): string
    {
        return 'List Unit';
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

                $sheet->mergeCells('A1:J1');
                $sheet->setCellValue('A1', 'LIST UNIT');

                $sheet->getStyle('A1')->getFont()
                    ->setBold(true)
                    ->setSize(16);

                $sheet->getStyle('A1')->getAlignment()
                    ->setHorizontal('center')
                    ->setVertical('center');

                $sheet->getRowDimension(1)->setRowHeight(40);
                $headerRow = 3;

                $headers = [
                    'A' => 'No',
                    'B' => 'VEHICLE',
                    'C' => 'GROUP',
                    'D' => 'TYPE',
                    'E' => 'IP ADDRESS',
                    'F' => 'STATUS CONVERTER',
                    'G' => 'TANGGAL PEMASANGAN',
                    'H' => 'JUMLAH RIWAYAT',
                    'I' => 'STATUS ENABLED',
                ];

                foreach ($headers as $col => $text) {
                    $sheet->setCellValue($col . $headerRow, $text);
                }

                $sheet->getStyle("A{$headerRow}:I{$headerRow}")
                    ->getFont()
                    ->setBold(true);

                $sheet->getStyle("A{$headerRow}:I{$headerRow}")
                    ->getAlignment()
                    ->setHorizontal('center')
                    ->setVertical('center')
                    ->setWrapText(true);

                $sheet->getStyle("A{$headerRow}:I{$headerRow}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('D9EAF7');

                $sheet->getRowDimension($headerRow)->setRowHeight(30);

                $row = $headerRow + 1;
                $no = 1;

                foreach ($this->listUnit as $unit) {

                    $sheet->setCellValue("A{$row}", $no++);

                    $sheet->setCellValue("B{$row}", $unit->VHC_ID ?? '');
                    $sheet->setCellValue("C{$row}", $unit->GROUP_ID ?? '');
                    $sheet->setCellValue("D{$row}", $unit->TYPE_ID ?? '');
                    $sheet->setCellValue("E{$row}", $unit->IP_ADDRESS ?? '');

                    $sheet->setCellValue(
                        "F{$row}",
                        $unit->STATUS_CONVERTER_OTOMATIS == 1
                            ? 'TERPASANG'
                            : 'BELUM TERPASANG'
                    );

                    $sheet->setCellValue(
                        "G{$row}",
                        $unit->TANGGAL_PEMASANGAN
                            ? \Carbon\Carbon::parse($unit->TANGGAL_PEMASANGAN)->format('d-m-Y H:i')
                            : '-'
                    );

                    $sheet->setCellValue(
                        "H{$row}",
                        $unit->JUMLAH_RIWAYAT ?? 0
                    );

                    $sheet->setCellValue(
                        "I{$row}",
                        $unit->STATUSENABLED ? 'ENABLED' : 'DISABLED'
                    );

                    $row++;
                }

                $lastRow = $row - 1;

                if ($lastRow >= $headerRow) {

                    $sheet->getStyle("A{$headerRow}:I{$lastRow}")
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);

                    $sheet->setAutoFilter("A{$headerRow}:I{$lastRow}");
                }

                $sheet->freezePane('A4');

                foreach (range('A', 'I') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $sheet->getStyle("A4:A{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal('center');

                $sheet->getStyle("F4:I{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal('center');

            },
        ];
    }
}
