<?php

namespace App\Exports;

use App\Models\Journal;
use App\Models\Submission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class FinanceReportExport implements FromCollection, WithHeadings, WithStyles, WithEvents, WithCustomStartCell
{
    protected $journal_id;
    protected $date_start;
    protected $date_end;

    public function __construct($journal_id, $date_start, $date_end)
    {
        $this->journal_id = $journal_id;
        $this->date_start = $date_start;
        $this->date_end = $date_end;
    }

    public function collection()
    {
        $journal_id = $this->journal_id;
        $date_start = $this->date_start;
        $date_end = $this->date_end;

        $data = Submission::with(['paymentInvoices.submission.issue.journal'])
            ->when($journal_id, function ($query) use ($journal_id) {
                return $query->whereHas('issue.journal', function ($q) use ($journal_id) {
                    $q->where('id', $journal_id);
                });
            })
            ->when($date_start, function ($query) use ($date_start) {
                return $query->whereHas('paymentInvoices', function ($q) use ($date_start) {
                    $q->whereDate('created_at', '>=', date('Y-m-d', strtotime($date_start)));
                });
            })
            ->when($date_end, function ($query) use ($date_end) {
                return $query->whereHas('paymentInvoices', function ($q) use ($date_end) {
                    $q->whereDate('created_at', '<=', date('Y-m-d', strtotime($date_end)));
                });
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($submission, $index) {
                return [
                    'no' => $index + 1,
                    'journal' => $submission->issue->journal->title ?? '-',
                    'authors' => $submission->authorsString,
                    'submission' => '(ID: ' . $submission->submission_id . ') ' . $submission->fullTitle,
                    'edition' => 'Vol. ' . $submission->issue->volume . ' No. ' . $submission->issue->number . ' (' . $submission->issue->year . '): ' . $submission->issue->title,
                    'total_pembyaran' => $submission->paymentInvoices->where('is_paid', 1)->sum('payment_amount'),
                    'loa' => (function () use ($submission) {
                        $authorId = $submission->authors[0]['id'] ?? null;
                        if (Storage::exists('arsip/loa/' . 'LoA-'  . $submission->submission_id  . '-' . $submission->id . '-' . $authorId . '.pdf')) {
                            return 'LoA Sudah Dikirim';
                        } else {
                            return 'LoA Belum Terbit';
                        }
                    })(),
                ];
            });

        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Jurnal',
            'Penulis',
            'Judul Pengajuan',
            'Edisi',
            'Total Pembayaran',
            'Status LoA',
        ];
    }

    public function startCell(): string
    {
        return 'A5';  // Mulai di baris 4
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

                // Menambahkan judul di baris 1
                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', 'Laporan Keuangan ' . Carbon::parse($this->date_start)->format('d M Y') . ' - ' . Carbon::parse($this->date_end)->format('d M Y'));
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Menambahkan tanggal di baris 2
                $sheet->mergeCells('A2:G2');
                $sheet->setCellValue('A2', 'Journal: ' . ($this->journal_id ? Journal::find($this->journal_id)->name : 'Semua Jurnal'));
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Menambahkan tanggal di baris 2
                $sheet->mergeCells('A3:G3');
                $sheet->setCellValue('A3', 'Import Tanggal: ' . date('d-m-Y H:i:s'));
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Tambahkan warna latar belakang untuk heading di baris ke-4
                $sheet->getStyle('A5:G5')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'FFFF00'],  // Warna kuning
                    ],
                ]);

                // Menambahkan border untuk heading
                $sheet->getStyle('A5:G5')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],  // Warna hitam
                        ],
                    ],
                ]);

                // Menambahkan border untuk data (mulai dari baris 5 sampai baris terakhir)
                $rowCount = $sheet->getHighestRow();
                $sheet->getStyle('A6:G' . $rowCount)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],  // Warna hitam
                        ],
                    ],
                ]);



                // Auto-fit kolom (menyesuaikan lebar kolom dengan konten)
                foreach (range('A', 'G') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
