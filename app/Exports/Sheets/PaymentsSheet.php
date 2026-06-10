<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class PaymentsSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $payments;

    public function __construct($payments)
    {
        $this->payments = $payments;
    }

    public function title(): string
    {
        return 'Pembayaran';
    }

    public function collection(): Collection
    {
        return $this->payments instanceof Collection ? $this->payments : collect($this->payments);
    }

    public function headings(): array
    {
        return ['#', 'Tanggal', 'Toko', 'Jumlah Bayar', 'Metode', 'Referensi', 'Catatan'];
    }

    public function map($payment): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $payment->payment_date ? $payment->payment_date->format('d/m/Y') : '-',
            $payment->debt->store->name ?? '-',
            'Rp ' . number_format($payment->amount_paid, 0, ',', '.'),
            $payment->payment_method ?? '-',
            $payment->reference_number ?? '-',
            $payment->notes ?? '-',
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 5, 'B' => 15, 'C' => 25, 'D' => 20, 'E' => 15, 'F' => 18, 'G' => 25];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFF9C4']]],
        ];
    }
}
