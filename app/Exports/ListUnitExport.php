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

class ListUnitExport implements FromCollection, WithEvents, WithStyles, WithDrawings, Responsable, WithTitle
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

                // ===== Judul =====
                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', 'LIST UNIT');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                // ===== Header =====
                $headerRow = 3;

                $headers = [
                    'A' => 'No',
                    'B' => 'VEHICLE',
                    'C' => 'GROUP',
                    'D' => 'TYPE',
                    'E' => 'IP ADDRESS',
                    'F' => 'STEP DOWN DC TO DC',
                    'G' => 'STATUS ENABLED',
                ];

                foreach ($headers as $col => $text) {
                    $sheet->setCellValue($col.$headerRow, $text);
                }

                $sheet->getStyle("A{$headerRow}:G{$headerRow}")
                    ->getFont()->setBold(true);

                $sheet->getStyle("A{$headerRow}:G{$headerRow}")
                    ->getAlignment()
                    ->setHorizontal('center')
                    ->setVertical('center');

                $sheet->getStyle("A{$headerRow}:G{$headerRow}")
                    ->getFill()
                    ->setFillType(
                        \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID
                    )
                    ->getStartColor()
                    ->setRGB('D9EAF7');

                // ===== Isi Data =====
                $row = $headerRow + 1;
                $no = 1;

                foreach ($this->listUnit as $unit) {

                    $sheet->setCellValue("A{$row}", $no++);
                    $sheet->setCellValue("B{$row}", $unit->VHC_ID);
                    $sheet->setCellValue("C{$row}", $unit->GROUP_ID ?? '');
                    $sheet->setCellValue("D{$row}", $unit->TYPE_ID ?? '');
                    $sheet->setCellValue("E{$row}", $unit->IP_ADDRESS ?? '');

                    $sheet->setCellValue(
                        "F{$row}",
                        $unit->CONVERTER_DC_TO_DC ? 'YES' : 'NO'
                    );

                    $sheet->setCellValue(
                        "G{$row}",
                        $unit->STATUSENABLED ? 'ENABLED' : 'DISABLED'
                    );

                    $row++;
                }

                $lastRow = $row - 1;

                // Border
                $sheet->getStyle("A{$headerRow}:G{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(
                        \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    );

                // Auto Filter
                $sheet->setAutoFilter("A{$headerRow}:G{$lastRow}");

                // Freeze Header
                $sheet->freezePane('A4');

                // Auto Width
                foreach (range('A', 'G') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Tengah untuk kolom tertentu
                $sheet->getStyle("A4:A{$lastRow}")
                    ->getAlignment()->setHorizontal('center');

                $sheet->getStyle("F4:G{$lastRow}")
                    ->getAlignment()->setHorizontal('center');
            }
        ];
    }
}
