<?php

namespace App\Exports;

use App\Models\Reviewer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReviewerAllExport implements FromCollection, WithHeadings, WithStyles, WithEvents, WithCustomStartCell
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Reviewer::with(['data'])
            ->latest()
            ->get()
            ->unique('reviewer_id')
            ->values() // Reset array keys
            ->map(function ($reviewer, $index) {
                $reviewer->journal = Reviewer::where('reviewer_id', $reviewer->reviewer_id)
                    ->with('issue.journal')
                    ->get()
                    ->map(function ($item) {
                        $journal_data = $item->issue->journal ?? null;
                        if ($journal_data) {
                            return (object) [
                                'id' => $journal_data->id,
                                'name' => $journal_data->name,
                                'title' => $journal_data->title,
                                'url_path' => $journal_data->url_path,
                            ];
                        }
                        return null;
                    })->filter();

                // Format data for Excel export
                return [
                    'no' => $index + 1, // Nomor urut
                    'reviewer_id' => $reviewer->reviewer_id,
                    'name' => $reviewer->name ?? '-',
                    'email' => $reviewer->email ?? '-',
                    'phone' => $reviewer->phone ?? '-',
                    'affiliation' => $reviewer->affiliation ?? '-',
                    'bank' => $reviewer->account_bank ?? '-',
                    'norek' => $reviewer->account_number ?? '-',
                    'npwp' => $reviewer->npwp ?? '-',
                    'journals' => $reviewer->journal->pluck('name')->unique()->implode(', '),
                    'total_journals' => $reviewer->journal->unique('id')->count(),
                ];
            });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Reviewer ID',
            'Name',
            'Email',
            'Phone',
            'Affiliation',
            'Bank',
            'No. Rekening',
            'NPWP',
            'Journals',
            'Total Journals',
        ];
    }

    /**
     * @return string
     */
    public function startCell(): string
    {
        return 'A4';  // Mulai di baris 4
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row (baris 4) as bold
            4 => ['font' => ['bold' => true]],
            // Style title (baris 1) as bold and larger
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 16
                ]
            ],
            // Style date (baris 2)
            2 => [
                'font' => [
                    'italic' => true,
                    'size' => 10
                ]
            ],
            // Auto-size columns
            'A:K' => ['alignment' => ['wrapText' => true]],
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Menambahkan judul di baris 1
                $sheet->setCellValue('A1', 'LAPORAN DATA REVIEWER');

                // Menambahkan tanggal export di baris 2
                $sheet->setCellValue('A2', 'Tanggal Export: ' . date('d F Y, H:i:s'));

                // Auto-size all columns
                foreach (range('A', 'K') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Set header background color (baris 4)
                $sheet->getStyle('A4:K4')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E2E8F0'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Add borders to all data cells
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle('A4:K' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Merge cells untuk judul
                $sheet->mergeCells('A1:K1');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Merge cells untuk tanggal
                $sheet->mergeCells('A2:K2');
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            },
        ];
    }
}
