<?php

namespace App\Exports;

use App\Models\Finance;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Fill;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Style\Border;

class CashflowExport implements FromCollection, WithHeadings, WithStyles, WithEvents, WithCustomStartCell
{
    protected $date_start;
    protected $date_end;
    protected $type;

    public function __construct($date_start, $date_end, $type = 'all')
    {
        $this->date_start = $date_start;
        $this->date_end = $date_end;
        $this->type = $type;
    }
    public function collection()
    {
        $counter = 1;
        $finance = Finance::where('date', '>=', $this->date_start)
            ->where('date', '<=', $this->date_end)
            ->get()
            ->map(function ($item) {
                return (object)[
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'type' => $item->type,
                    'amount' => $item->amount,
                    'date' => $item->date,
                    'payment_method' => $item->payment_method,
                    'payment_reference' => $item->payment_reference,
                    'payment_note' => $item->payment_note,
                    'attachment' => $item->attachment,
                    'editable' => true,
                    'created_at' => $item->created_at,
                    'created_by' => $item->created_by,
                    'updated_at' => $item->updated_at,
                    'updated_by' => $item->updated_by,
                ];
            })->collect();

        $billing = Payment::with(['paymentInvoice'])
            ->whereBetween('created_at', [$this->date_start, $this->date_end])
            ->where('payment_status', 'accepted')
            ->get()
            ->map(function ($item) {
                return (object)[
                    'id' => null,
                    'name' => 'Pembayaran Invoice ' . ($item->paymentInvoice->invoice_number ?? 'Unknown Invoice')  . "/JRNL/UINSMDD/" . ($item->paymentInvoice->created_at ? $item->paymentInvoice->created_at->format('Y') : '-'),
                    'description' => 'Pembayaran Invoice ' . ($item->paymentInvoice->invoice_number ?? 'Unknown Invoice') . "/JRNL/UINSMDD/" . ($item->paymentInvoice->created_at ? $item->paymentInvoice->created_at->format('Y') : '-') . ' Yang Telah Dibayarkan Oleh ' . ($item->name ?? 'Unknown Payer'),
                    'type' => 'income',
                    'amount' => $item->paymentInvoice->payment_amount ?? 0,
                    'date' => $item->payment_timestamp,
                    'payment_method' => ($item->payment_method ?? "-") . ' a/n ' . ($item->payment_account_name ?? "-"),
                    'payment_reference' => "-",
                    'payment_note' => $item->payment_note,
                    'attachment' => $item->payment_file,
                    'editable' => false,
                    'created_at' => $item->created_at,
                    'created_by' => $item->created_by ?? '-',
                    'updated_at' => $item->updated_at,
                    'updated_by' => $item->updated_by ?? '-',
                ];
            })->collect();


        $data = $finance->merge($billing)->when($this->type != 'all', function ($query) {
            return $query->where('type', $this->type);
        })->sortByDesc('date')->values();


        return $data->map(function ($item) use (&$counter) {
            return (object)[
                'No' => $counter++,
                'name' => $item->name,
                'description' => $item->description,
                'type' => $item->type,
                'amount' => $item->amount,
                'date' => Carbon::parse($item->date)->format('Y-m-d'),
                'payment_method' => $item->payment_method,
                'payment_reference' => $item->payment_reference,
                'payment_note' => $item->payment_note,
                'attachment' => $item->attachment ? '=HYPERLINK("' . Storage::url($item->attachment) . '", "Lihat Lampiran")' : '-',
                'created_at' => Carbon::parse($item->created_at)->format('Y-m-d H:i:s') . " (Oleh: " . ($item->created_by ? (User::find($item->created_by)->name ?? "-") : '-') . ")",
                'updated_at' => Carbon::parse($item->updated_at)->format('Y-m-d H:i:s') . " (Oleh: " . ($item->updated_by ? (User::find($item->updated_by)->name ?? "-") : '-') . ")",
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'Deskripsi',
            'Tipe',
            'Jumlah',
            'Tanggal',
            'Metode Pembayaran',
            'Payment Reference',
            'Note',
            'Lampiran',
            'Dibuat Pada',
            'Diperbarui Pada',
        ];
    }

    public function startCell(): string
    {
        return 'A4';  // Mulai di baris 4
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
                $sheet->mergeCells('A1:M1');
                $sheet->setCellValue('A1', 'Laporan Keuangan ' . Carbon::parse($this->date_start)->format('d M Y') . ' - ' . Carbon::parse($this->date_end)->format('d M Y'));
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Menambahkan tanggal di baris 2
                $sheet->mergeCells('A2:M2');
                $sheet->setCellValue('A2', 'Import Tanggal: ' . date('d-m-Y H:i:s'));
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Tambahkan warna latar belakang untuk heading di baris ke-4
                $sheet->getStyle('A4:L4')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'FFFF00'],  // Warna kuning
                    ],
                ]);

                // Menambahkan border untuk heading
                $sheet->getStyle('A4:L4')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],  // Warna hitam
                        ],
                    ],
                ]);

                // Menambahkan border untuk data (mulai dari baris 5 sampai baris terakhir)
                $rowCount = $sheet->getHighestRow();
                $sheet->getStyle('A5:L' . $rowCount)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],  // Warna hitam
                        ],
                    ],
                ]);

                //jika row 5 income maka warna hijau, jika row 5 expense maka warna merah
                $conditionalStyles = $sheet->getStyle('D5:D' . $rowCount)->getConditionalStyles();

                $incomeCondition = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
                $incomeCondition->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_EXPRESSION);
                $incomeCondition->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_NONE);
                $incomeCondition->setConditions(['D5="income"']);
                $incomeCondition->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('00FF00');

                $expenseCondition = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
                $expenseCondition->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_EXPRESSION);
                $expenseCondition->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_NONE);
                $expenseCondition->setConditions(['D5="expense"']);
                $expenseCondition->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FF0000');

                $conditionalStyles[] = $incomeCondition;
                $conditionalStyles[] = $expenseCondition;

                $sheet->getStyle('D5:E' . $rowCount)->setConditionalStyles($conditionalStyles);

                // Auto-fit kolom (menyesuaikan lebar kolom dengan konten)
                foreach (range('A', 'L') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
