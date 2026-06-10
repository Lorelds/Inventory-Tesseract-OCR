<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SummarySheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function columnWidths(): array
    {
        return ['A' => 30, 'B' => 25];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '666666']]],
            4 => ['font' => ['bold' => true, 'size' => 13]],
            5 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E8F5E9']]],
            11 => ['font' => ['bold' => true, 'size' => 13]],
            12 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFF3E0']]],
            18 => ['font' => ['bold' => true, 'size' => 13]],
            19 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E3F2FD']]],
        ];
    }

    public function array(): array
    {
        return [
            [$this->data['companyName']],
            ['Laporan Periode: ' . $this->data['periodLabel']],
            ['Dibuat: ' . $this->data['generatedAt']],
            [],
            // Sales Section
            ['📊 PENJUALAN'],
            ['Metrik', 'Nilai'],
            ['Jumlah Transaksi', $this->data['salesCount']],
            ['Total Penjualan', 'Rp ' . number_format($this->data['totalSales'], 0, ',', '.')],
            ['Sudah Dibayar', 'Rp ' . number_format($this->data['totalSalesPaid'], 0, ',', '.')],
            ['Belum Dibayar', 'Rp ' . number_format($this->data['totalSales'] - $this->data['totalSalesPaid'], 0, ',', '.')],
            [],
            // Purchases Section
            ['📦 PEMBELIAN'],
            ['Metrik', 'Nilai'],
            ['Jumlah Transaksi', $this->data['purchasesCount']],
            ['Total Pembelian', 'Rp ' . number_format($this->data['totalPurchases'], 0, ',', '.')],
            ['Sudah Dibayar', 'Rp ' . number_format($this->data['totalPurchasesPaid'], 0, ',', '.')],
            ['Belum Dibayar', 'Rp ' . number_format($this->data['totalPurchases'] - $this->data['totalPurchasesPaid'], 0, ',', '.')],
            [],
            // Debts Section
            ['💰 HUTANG'],
            ['Metrik', 'Nilai'],
            ['Hutang Baru', 'Rp ' . number_format($this->data['totalDebtCreated'], 0, ',', '.')],
            ['Pembayaran Hutang', 'Rp ' . number_format($this->data['totalDebtPaid'], 0, ',', '.')],
            ['Total Sisa Hutang', 'Rp ' . number_format($this->data['totalOutstanding'], 0, ',', '.')],
            [],
            // Products Section
            ['📋 INVENTARIS'],
            ['Metrik', 'Nilai'],
            ['Total Produk', $this->data['totalProducts']],
            ['Nilai Inventaris', 'Rp ' . number_format($this->data['totalInventoryValue'], 0, ',', '.')],
            ['Stok Rendah (≤5)', $this->data['lowStockProducts']->count()],
            ['Stok Habis', $this->data['outOfStockProducts']->count()],
        ];
    }
}
