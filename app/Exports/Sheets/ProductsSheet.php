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

class ProductsSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function title(): string
    {
        return 'Produk';
    }

    public function collection(): Collection
    {
        return $this->products instanceof Collection ? $this->products : collect($this->products);
    }

    public function headings(): array
    {
        return ['#', 'SKU', 'Nama Produk', 'Kategori', 'Harga Beli', 'Harga Jual', 'Stok', 'Nilai Stok', 'Status'];
    }

    public function map($product): array
    {
        static $index = 0;
        $index++;

        $status = 'OK';
        if ($product->stock == 0) $status = 'Habis';
        elseif ($product->stock <= 5) $status = 'Rendah';

        return [
            $index,
            $product->sku,
            $product->name,
            $product->category ?? '-',
            'Rp ' . number_format($product->buy_price, 0, ',', '.'),
            'Rp ' . number_format($product->sell_price, 0, ',', '.'),
            $product->stock,
            'Rp ' . number_format($product->buy_price * $product->stock, 0, ',', '.'),
            $status,
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 5, 'B' => 18, 'C' => 30, 'D' => 15, 'E' => 18, 'F' => 18, 'G' => 8, 'H' => 20, 'I' => 10];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'C8E6C9']]],
        ];
    }
}
