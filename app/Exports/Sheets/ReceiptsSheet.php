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

class ReceiptsSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $receipts;
    protected string $sheetTitle;

    public function __construct($receipts, string $sheetTitle)
    {
        $this->receipts = $receipts;
        $this->sheetTitle = $sheetTitle;
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function collection(): Collection
    {
        return $this->receipts instanceof Collection ? $this->receipts : collect($this->receipts);
    }

    public function headings(): array
    {
        return ['#', 'Tanggal', 'Toko', 'Total', 'Status Bayar', 'Status'];
    }

    public function map($receipt): array
    {
        static $index = 0;
        $index++;

        $paymentLabel = match($receipt->payment_status) {
            'lunas'  => 'Lunas',
            'hutang'  => 'Hutang',
            'partial' => 'Partial',
            default   => $receipt->payment_status,
        };

        return [
            $index,
            $receipt->transaction_date ? \Carbon\Carbon::parse($receipt->transaction_date)->format('d/m/Y') : '-',
            $receipt->store->name ?? $receipt->store_name ?? '-',
            'Rp ' . number_format($receipt->total_amount, 0, ',', '.'),
            $paymentLabel,
            ucfirst($receipt->status),
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 5, 'B' => 15, 'C' => 25, 'D' => 20, 'E' => 15, 'F' => 12];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E8EAF6']]],
        ];
    }
}
