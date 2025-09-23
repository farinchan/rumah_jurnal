<?php

namespace App\Exports;

use App\Models\EventAttendance;
use App\Models\EventUser;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EventAttendanceUserExport implements FromCollection, WithHeadings, WithStyles, WithEvents, WithCustomStartCell
{
     protected $id;
    protected $attendanceId;

    public function __construct($id, $attendanceId)
    {
        $this->id = $id;
        $this->attendanceId = $attendanceId;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $attendance = EventAttendance::with(['attendances', 'event'])->find($this->attendanceId);
        if (!$attendance) {
            return collect([]);
        }

        $userAttendances = EventUser::where('event_id', $this->id)
            ->with(['user'])
            ->get()
            ->map(function ($user, $index) use ($attendance) {
                $attendanceUser = $attendance->attendances->where('event_user_id', $user->id)->first();

                return [
                    'no' => $index + 1,
                    'name' => $user->name ?? '-',
                    'email' => $user->email ?? '-',
                    'phone' => $user->phone ?? '-',
                    'status_kehadiran' => $attendanceUser ? 'Hadir' : 'Tidak Hadir',
                    'waktu_kehadiran' => $attendanceUser ?
                        \Carbon\Carbon::parse($attendanceUser->attendance_datetime)->format('d M Y H:i:s') : '-',
                    'ip_address' => $attendanceUser ? ($attendanceUser->ip_address ?? '-') : '-',
                    'user_agent' => $attendanceUser ? ($attendanceUser->user_agent ?? '-') : '-',
                ];
            });

        return $userAttendances;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama Peserta',
            'Email',
            'No. Telepon',
            'Status Kehadiran',
            'Waktu Kehadiran',
            'IP Address',
            'User Agent',
        ];
    }

    /**
     * @return string
     */
    public function startCell(): string
    {
        return 'A5';  // Mulai di baris 5
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row (baris 5) as bold
            5 => ['font' => ['bold' => true]],
            // Style title (baris 1) as bold and larger
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 16
                ]
            ],
            // Style event info (baris 2)
            2 => [
                'font' => [
                    'bold' => true,
                    'size' => 12
                ]
            ],
            // Style date (baris 3)
            3 => [
                'font' => [
                    'italic' => true,
                    'size' => 10
                ]
            ],
            // Auto-size columns
            'A:H' => ['alignment' => ['wrapText' => true]],
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

                // Get attendance info for title
                $attendance = EventAttendance::with(['event'])->find($this->attendanceId);
                $eventName = $attendance ? $attendance->event->name : 'Event';
                $attendanceName = $attendance ? $attendance->name : 'Kehadiran';

                // Menambahkan judul di baris 1
                $sheet->setCellValue('A1', 'LAPORAN KEHADIRAN PESERTA EVENT');

                // Menambahkan info event di baris 2
                $sheet->setCellValue('A2', 'Event: ' . $eventName . ' - ' . $attendanceName);

                // Menambahkan tanggal export di baris 3
                $sheet->setCellValue('A3', 'Tanggal Export: ' . date('d F Y, H:i:s'));

                // Auto-size all columns
                foreach (range('A', 'H') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Set header background color (baris 5)
                $sheet->getStyle('A5:H5')->applyFromArray([
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
                $sheet->getStyle('A5:H' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Merge cells untuk judul
                $sheet->mergeCells('A1:H1');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Merge cells untuk info event
                $sheet->mergeCells('A2:H2');
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                // Merge cells untuk tanggal
                $sheet->mergeCells('A3:H3');
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            },
        ];
    }
}
