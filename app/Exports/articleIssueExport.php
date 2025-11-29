<?php

namespace App\Exports;

use App\Models\Issue;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;


class articleIssueExport implements WithStyles, WithEvents, WithCustomStartCell
{
    protected $issue_id;

    // Constructor untuk menerima parameter classroom
    public function __construct($issue_id)
    {
        $this->issue_id = $issue_id;
    }
    // public function collection()
    // {
    //     return $issue = Issue::with(['submissions'])
    //         ->where('id', $this->issue_id)
    //         ->first()->submissions
    //         ->map(function ($submissions) {
    //             return [
    //                 'submission_id' => $submissions->submission_id,
    //                 'authors' => $submissions->authorsString,
    //                 'title' => $submissions->FullTitle,
    //                 'status' => $submissions->status_label,
    //                 'url_published' => $submissions->urlPublished ? '=HYPERLINK("' . $submissions->urlPublished . '", "lihat")' : '',
    //                 'editors' => $submissions->editors->pluck('name')->implode(", "),
    //                 'reviewers' => $submissions->reviewers->pluck('name')->implode(", "),
    //             ];
    //         });
    // }

    // public function headings(): array
    // {
    //     return [
    //         'ID Artikel',
    //         'Penulis',
    //         'Judul',
    //         'Status',
    //         'Link Publish',
    //         'Editor',
    //         'Reviewer',
    //     ];
    // }

    public function startCell(): string
    {
        return 'A8';  // Mulai di baris 4
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

                $sheet->mergeCells('A1:I1');

                $issue = Issue::with('journal')->where('id', $this->issue_id)->first();
                $submissions = $issue->submissions;

                $sheet->setCellValue('A1', 'Daftar Artikel Jurnal');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A2:I2');
                $sheet->setCellValue('A2', 'Import Tanggal: ' . date('d-m-Y H:i:s'));
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A4:I4');
                $sheet->getStyle('A4:I4')->getFont()->setBold(true);
                $sheet->setCellValue('A4', 'Jurnal: ' . $issue->journal->title);

                $sheet->mergeCells('A5:I5');
                $sheet->getStyle('A5:I5')->getFont()->setBold(true);
                $sheet->setCellValue('A5', 'Issue: Vol.' . $issue->volume . ' No.' . $issue->number . '  (' . $issue->year . '): ' . $issue->title);

                $sheet->mergeCells('A6:I6');
                $sheet->getStyle('A6:I6')->getFont()->setBold(true);
                $sheet->setCellValue('A6', 'Total Article: ' . $issue->submissions->count());

                $sheet->getStyle('A8:I9')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'FFFF00'],  // Warna kuning
                    ],
                ]);

                // Menambahkan border untuk heading
                $sheet->getStyle('A8:I9')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],  // Warna hitam
                        ],
                    ],
                ]);

                $sheet->setCellValue('A8', 'ID Artikel');
                $sheet->mergeCells('A8:A9');
                $sheet->getStyle('A8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->setCellValue('B8', 'Penulis');
                $sheet->mergeCells('B8:C8');
                $sheet->getStyle('B8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->setCellValue('B9', 'Nama');
                $sheet->getStyle('B9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('C9', 'Affiliasi');
                $sheet->getStyle('C9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('D8', 'Judul');
                $sheet->mergeCells('D8:D9');
                $sheet->getStyle('D8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->setCellValue('E8', 'Status');
                $sheet->mergeCells('E8:E9');
                $sheet->getStyle('E8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->setCellValue('F8', 'Link Publish');
                $sheet->mergeCells('F8:F9');
                $sheet->getStyle('F8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->setCellValue('G8', 'Editor');
                $sheet->mergeCells('G8:G9');
                $sheet->getStyle('G8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->setCellValue('H8', 'Reviewer');
                $sheet->mergeCells('H8:I8');
                $sheet->getStyle('H8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->setCellValue('H9', 'Nama');
                $sheet->getStyle('H9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('I9', 'Affiliasi');
                $sheet->getStyle('I9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('A8:I9')->getFont()->setBold(true);

                 $currentRow = 10;

                 foreach ($submissions as $index => $submission) {
                     $startRow = $currentRow;
                     $authorCount = is_array($submission->authors) ? count($submission->authors) : $submission->authors->count();
                     $reviewerCount = is_array($submission->reviewers) ? count($submission->reviewers) : $submission->reviewers->count();
                     $maxRows = max($authorCount, $reviewerCount, 1);

                     // Merge cells untuk kolom yang tidak perlu diulang
                     if ($maxRows > 1) {
                         $endRow = $startRow + $maxRows - 1;
                         $sheet->mergeCells('A' . $startRow . ':A' . $endRow);
                         $sheet->mergeCells('D' . $startRow . ':D' . $endRow);
                         $sheet->mergeCells('E' . $startRow . ':E' . $endRow);
                         $sheet->mergeCells('F' . $startRow . ':F' . $endRow);
                         $sheet->mergeCells('G' . $startRow . ':G' . $endRow);
                     }

                     // Set nilai untuk kolom yang di-merge
                     $sheet->setCellValue('A' . $startRow, $submission->submission_id);
                     $sheet->setCellValue('D' . $startRow, $submission->FullTitle);
                     $sheet->setCellValue('E' . $startRow, $submission->status_label);

                     if ($submission->urlPublished) {
                         $sheet->setCellValue('F' . $startRow, '=HYPERLINK("' . $submission->urlPublished . '", "lihat")');
                         $sheet->getStyle('F' . $startRow)->getFont()->getColor()->setRGB('0000FF');
                         $sheet->getStyle('F' . $startRow)->getFont()->setUnderline(true);
                     }

                     $sheet->setCellValue('G' . $startRow, $submission->editors->pluck('name')->implode(", "));

                     // Align center vertical untuk merged cells
                     if ($maxRows > 1) {
                         $sheet->getStyle('A' . $startRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                         $sheet->getStyle('D' . $startRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                         $sheet->getStyle('E' . $startRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                         $sheet->getStyle('F' . $startRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                         $sheet->getStyle('G' . $startRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                     }

                     // Isi authors dan affiliations per baris
                     foreach ($submission->authors as $authorIndex => $author) {
                         $row = $startRow + $authorIndex;
                         $sheet->setCellValue('B' . $row, $author["name"]);
                         $sheet->setCellValue('C' . $row, $author["affiliation"]);
                     }

                     // Isi reviewers per baris
                     foreach ($submission->reviewers as $reviewerIndex => $reviewer) {
                         $row = $startRow + $reviewerIndex;
                         $sheet->setCellValue('H' . $row, $reviewer->name);
                            $sheet->setCellValue('I' . $row, $reviewer->affiliation);
                     }

                     $currentRow += $maxRows;
                 }

                // Menambahkan border untuk data (mulai dari baris 5 sampai baris terakhir)
                $rowCount = $sheet->getHighestRow();
                $sheet->getStyle('A9:I' . $rowCount)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],  // Warna hitam
                        ],
                    ],
                ]);

                $sheet->getStyle('E9:E' . $rowCount)->applyFromArray([
                    'font' => [
                        'color' => ['rgb' => '0000FF'],  // Warna biru
                    ],
                ]);

                // Auto-fit kolom (menyesuaikan lebar kolom dengan konten)
                foreach (range('A', 'I') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
