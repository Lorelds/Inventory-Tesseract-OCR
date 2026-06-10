<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportExport implements WithMultipleSheets
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            'Ringkasan'  => new Sheets\SummarySheet($this->data),
            'Penjualan'  => new Sheets\ReceiptsSheet($this->data['salesReceipts'], 'Penjualan'),
            'Pembelian'  => new Sheets\ReceiptsSheet($this->data['purchaseReceipts'], 'Pembelian'),
            'Pembayaran' => new Sheets\PaymentsSheet($this->data['payments']),
            'Produk'     => new Sheets\ProductsSheet($this->data['products']),
        ];
    }
}
