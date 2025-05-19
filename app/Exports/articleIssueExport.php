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


class articleIssueExport implements FromCollection, WithHeadings, WithStyles, WithEvents, WithCustomStartCell
{
    protected $issue_id;

    // Constructor untuk menerima parameter classroom
    public function __construct($issue_id)
    {
        $this->issue_id = $issue_id;
    }
    public function collection()
    {
        return $issue = Issue::with(['submissions'])
            ->where('id', $this->issue_id)
            ->first()->submissions
            ->map(function ($submissions) {
                return [
                    'submission_id' => $submissions->submission_id,
                    'authors' => $submissions->authorsString,
                    'title' => $submissions->FullTitle,
                    'status' => $submissions->status_label,
                    'url_published' => $submissions->urlPublished ? '=HYPERLINK("' . $submissions->urlPublished . '", "lihat")' : '',
                    'editors' => $submissions->editors->pluck('name')->implode(", "),
                    'reviewers' => $submissions->reviewers->pluck('name')->implode(", "),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID Artikel',
            'Penulis',
            'Judul',
            'Status',
            'Link Publish',
            'Editor',
            'Reviewer',
        ];
    }

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

                $sheet->mergeCells('A1:G1');

                $issue = Issue::with('journal')->where('id', $this->issue_id)->first();
                $sheet->setCellValue('A1', 'Daftar Artikel Jurnal');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A2:G2');
                $sheet->setCellValue('A2', 'Import Tanggal: ' . date('d-m-Y H:i:s'));
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A4:G4');
                $sheet->getStyle('A4:G4')->getFont()->setBold(true);
                $sheet->setCellValue('A4', 'Jurnal: ' . $issue->journal->title);

                $sheet->mergeCells('A5:G5');
                $sheet->getStyle('A5:G5')->getFont()->setBold(true);
                $sheet->setCellValue('A5', 'Issue: Vol.' . $issue->volume . ' No.' . $issue->number . '  (' . $issue->year . '): ' . $issue->title);

                $sheet->mergeCells('A6:G6');
                $sheet->getStyle('A6:G6')->getFont()->setBold(true);
                $sheet->setCellValue('A6', 'Total Article: ' . $issue->submissions->count());

                $sheet->getStyle('A8:G8')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'FFFF00'],  // Warna kuning
                    ],
                ]);

                // Menambahkan border untuk heading
                $sheet->getStyle('A8:G8')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],  // Warna hitam
                        ],
                    ],
                ]);

                // Menambahkan border untuk data (mulai dari baris 5 sampai baris terakhir)
                $rowCount = $sheet->getHighestRow();
                $sheet->getStyle('A9:G' . $rowCount)->applyFromArray([
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
                foreach (range('A', 'B') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
