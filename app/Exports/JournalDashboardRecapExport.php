<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class JournalDashboardRecapExport implements FromView, ShouldAutoSize, WithEvents
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('back.pages.dashboard.export-journal-recap', [
            'journal' => $this->data['journal'] ?? [],
            'is_scope' => $this->data['is_scope'] ?? false,
            'summary' => $this->data['summary'] ?? [],
            'issues_table' => $this->data['issues_table'] ?? [],
            'journals_table' => $this->data['journals_table'] ?? [],
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Set default font for the whole sheet
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->getFont()->setName('Calibri');

                // Auto row height for header info
                $sheet->getRowDimension(1)->setRowHeight(28);

                // Add grid borders to all data cells starting from table header row (row 7)
                $tableRange = 'A7:' . $highestColumn . $highestRow;
                $sheet->getStyle($tableRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D0D5DD'],
                        ],
                    ],
                ]);

                // Table Header row (row 7) styling
                $headerRange = 'A7:' . $highestColumn . '7';
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '1E1E2D'],
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E4E6EF'],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(7)->setRowHeight(26);

                // Total Row (last row) styling
                $totalRowRange = 'A' . $highestRow . ':' . $highestColumn . $highestRow;
                $sheet->getStyle($totalRowRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '1E1E2D'],
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF8DD'],
                    ],
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => 'A1A5B7'],
                        ],
                        'bottom' => [
                            'borderStyle' => Border::BORDER_DOUBLE,
                            'color' => ['rgb' => 'A1A5B7'],
                        ],
                    ],
                ]);
                $sheet->getRowDimension($highestRow)->setRowHeight(24);
            },
        ];
    }
}
