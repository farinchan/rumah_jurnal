<?php

namespace App\Exports;

use App\Models\Issue;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;

class EditorExport implements WithStyles, WithEvents
{
    protected $issue_id;

    // Constructor untuk menerima parameter classroom
    public function __construct($issue_id)
    {
        $this->issue_id = $issue_id;
    }
    public function styles($sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();


                $issue = Issue  ::with('journal')->where('id', $this->issue_id)->first();
                $editors = $issue->editors;
                $sheet->mergeCells('A1:F1');
                $sheet->setCellValue('A1', 'Editor Report');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A2:F2');
                $sheet->setCellValue('A2', 'Import Tanggal: ' . date('d-m-Y H:i:s'));
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A4:F4');
                $sheet->getStyle('A4:F4')->getFont()->setBold(true);
                $sheet->setCellValue('A4', 'Jurnal: ' . $issue->journal->title);

                $sheet->mergeCells('A5:F5');
                $sheet->getStyle('A5:F5')->getFont()->setBold(true);
                $sheet->setCellValue('A5', 'Issue: Vol.' . $issue->volume . ' No.' . $issue->number . '  (' . $issue->year . '): ' . $issue->title);

                $sheet->mergeCells('A6:F6');
                $sheet->getStyle('A6:F6')->getFont()->setBold(true);
                $sheet->setCellValue('A6', 'Total Editor: ' . $issue->editors->count());

                $sheet->getStyle('A8:F8')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                    ],
                ]);

                // Menambahkan border untuk heading
                $sheet->getStyle('A8:F8')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],  // Warna hitam
                        ],
                    ],
                ]);

                $sheet->setCellValue('A8', 'No');
                $sheet->setCellValue('B8', 'Nama Editor');
                $sheet->setCellValue('C8', 'Affiliasi');
                $sheet->setCellValue('D8', 'Jumlah Artikel');
                $sheet->setCellValue('E8', 'Judul Artikel');
                $sheet->setCellValue('F8', 'Status');
                $sheet->getStyle('A8:F8')->getFont()->setBold(true);

                $currentRow = 9;
                foreach ($editors as $index => $editor) {
                    $titles = $editor->submissionsEdited->pluck('submission.fullTitle')->toArray();
                    // Tambahkan nomor di setiap judul
                    foreach ($titles as $i => $title) {
                        $titles[$i] = ($i + 1) . '. ' . $title;
                    }
                    $articleCount = count($titles);

                    $startRow = $currentRow;
                    $endRow = $startRow + ($articleCount > 0 ? $articleCount - 1 : 0);

                    // Merge cells for reviewer info if multiple titles
                    if ($articleCount > 1) {
                        $sheet->mergeCells("A{$startRow}:A{$endRow}");
                        $sheet->mergeCells("B{$startRow}:B{$endRow}");
                        $sheet->mergeCells("C{$startRow}:C{$endRow}");
                        $sheet->mergeCells("D{$startRow}:D{$endRow}");
                        $sheet->mergeCells("F{$startRow}:F{$endRow}");

                        // Set vertical alignment (rata atas bawah) for merged cells
                        foreach (['A', 'B', 'C', 'D', 'F'] as $col) {
                            $sheet->getStyle("{$col}{$startRow}:{$col}{$endRow}")
                                ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)
                                ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        }
                    }

                    for ($i = 0; $i < $articleCount; $i++) {
                        $row = $startRow + $i;
                        if ($i === 0) {
                            $sheet->setCellValue('A' . $row, $index + 1);
                            $sheet->setCellValue('B' . $row, $editor->name);
                            $sheet->setCellValue('C' . $row, $editor->affiliation);
                            $sheet->setCellValue('D' . $row, $articleCount);
                            $sheet->setCellValue('F' . $row, "Editor");
                        }
                        $sheet->setCellValue('E' . $row, $titles[$i]);
                    }

                    // Jika tidak ada artikel, tetap isi satu baris
                    if ($articleCount === 0) {
                        $sheet->setCellValue('A' . $startRow, $index + 1);
                        $sheet->setCellValue('B' . $startRow, $editor->name);
                        $sheet->setCellValue('C' . $startRow, $editor->affiliation);
                        $sheet->setCellValue('D' . $startRow, 0);
                        $sheet->setCellValue('E' . $startRow, '-');
                        $sheet->setCellValue('F' . $startRow, "Editor");
                        // Set vertical alignment for single row
                        foreach (['A', 'B', 'C', 'D', 'F'] as $col) {
                            $sheet->getStyle("{$col}{$startRow}")
                                ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)
                                ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        }
                        $currentRow++;
                    } else {
                        $currentRow += $articleCount;
                    }
                }

                // Menambahkan border untuk data (mulai dari baris 5 sampai baris terakhir)
                $rowCount = $sheet->getHighestRow();
                $sheet->getStyle('A9:F' . $rowCount)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],  // Warna hitam
                        ],
                    ],
                ]);

                // Auto-fit kolom (menyesuaikan lebar kolom dengan konten)
                foreach (range('A', 'F') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
