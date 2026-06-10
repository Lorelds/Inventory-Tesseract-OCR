<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan {{ $periodLabel }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a2e; line-height: 1.6; }
        
        .header { text-align: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 3px solid #3b82f6; }
        .header h1 { font-size: 22px; color: #1e3a5f; margin-bottom: 2px; }
        .header .subtitle { font-size: 13px; color: #64748b; }
        .header .period { font-size: 14px; color: #3b82f6; font-weight: bold; margin-top: 5px; }
        .header .meta { font-size: 9px; color: #94a3b8; margin-top: 3px; }

        .section { margin-bottom: 20px; }
        .section-title { font-size: 14px; font-weight: bold; color: #1e3a5f; padding: 6px 10px; background: #eff6ff; border-left: 4px solid #3b82f6; margin-bottom: 10px; }

        .summary-grid { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .summary-grid td { padding: 8px 12px; border: 1px solid #e2e8f0; }
        .summary-grid .label { color: #64748b; width: 55%; background: #f8fafc; }
        .summary-grid .value { font-weight: bold; text-align: right; }
        .summary-grid .value.green { color: #16a34a; }
        .summary-grid .value.red { color: #dc2626; }
        .summary-grid .value.blue { color: #2563eb; }
        .summary-grid .value.orange { color: #ea580c; }

        .stats-row { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .stats-row td { width: 25%; text-align: center; padding: 12px 5px; border: 1px solid #e2e8f0; }
        .stats-row .stat-value { font-size: 20px; font-weight: bold; display: block; }
        .stats-row .stat-label { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }

        table.data-table { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.data-table thead th { background: #1e3a5f; color: white; padding: 6px 8px; text-align: left; font-weight: 600; }
        table.data-table tbody td { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; }
        table.data-table tbody tr:nth-child(even) { background: #f8fafc; }
        table.data-table .text-right { text-align: right; }
        table.data-table .text-center { text-align: center; }

        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }
        .badge-blue { background: #dbeafe; color: #1e40af; }

        .footer { text-align: center; font-size: 9px; color: #94a3b8; margin-top: 30px; padding-top: 10px; border-top: 1px solid #e2e8f0; }
        .page-break { page-break-after: always; }

        .two-col { width: 100%; }
        .two-col td { width: 50%; vertical-align: top; padding-right: 8px; }
        .two-col td:last-child { padding-right: 0; padding-left: 8px; }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <h1>{{ $companyName }}</h1>
        <div class="subtitle">Laporan Keuangan & Inventaris</div>
        <div class="period">{{ $periodLabel }}</div>
        <div class="meta">Dibuat: {{ $generatedAt }} | {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</div>
    </div>

    <!-- QUICK STATS -->
    <table class="stats-row">
        <tr>
            <td>
                <span class="stat-value" style="color: #16a34a;">Rp {{ number_format($totalSales, 0, ',', '.') }}</span>
                <span class="stat-label">Total Penjualan</span>
            </td>
            <td>
                <span class="stat-value" style="color: #ea580c;">Rp {{ number_format($totalPurchases, 0, ',', '.') }}</span>
                <span class="stat-label">Total Pembelian</span>
            </td>
            <td>
                <span class="stat-value" style="color: #dc2626;">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</span>
                <span class="stat-label">Sisa Hutang</span>
            </td>
            <td>
                <span class="stat-value" style="color: #2563eb;">{{ $totalProducts }}</span>
                <span class="stat-label">Total Produk</span>
            </td>
        </tr>
    </table>

    <!-- SALES & PURCHASES SUMMARY -->
    <table class="two-col">
        <tr>
            <td>
                <div class="section">
                    <div class="section-title">Ringkasan Penjualan</div>
                    <table class="summary-grid">
                        <tr><td class="label">Jumlah Transaksi</td><td class="value">{{ $salesCount }}</td></tr>
                        <tr><td class="label">Total Penjualan</td><td class="value green">Rp {{ number_format($totalSales, 0, ',', '.') }}</td></tr>
                        <tr><td class="label">Sudah Dibayar</td><td class="value green">Rp {{ number_format($totalSalesPaid, 0, ',', '.') }}</td></tr>
                        <tr><td class="label">Belum Dibayar</td><td class="value red">Rp {{ number_format($totalSales - $totalSalesPaid, 0, ',', '.') }}</td></tr>
                    </table>
                </div>
            </td>
            <td>
                <div class="section">
                    <div class="section-title">Ringkasan Pembelian</div>
                    <table class="summary-grid">
                        <tr><td class="label">Jumlah Transaksi</td><td class="value">{{ $purchasesCount }}</td></tr>
                        <tr><td class="label">Total Pembelian</td><td class="value orange">Rp {{ number_format($totalPurchases, 0, ',', '.') }}</td></tr>
                        <tr><td class="label">Sudah Dibayar</td><td class="value green">Rp {{ number_format($totalPurchasesPaid, 0, ',', '.') }}</td></tr>
                        <tr><td class="label">Belum Dibayar</td><td class="value red">Rp {{ number_format($totalPurchases - $totalPurchasesPaid, 0, ',', '.') }}</td></tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- DEBTS SUMMARY -->
    <div class="section">
        <div class="section-title">Hutang & Pembayaran</div>
        <table class="summary-grid">
            <tr><td class="label">Hutang Baru (Periode Ini)</td><td class="value red">Rp {{ number_format($totalDebtCreated, 0, ',', '.') }}</td></tr>
            <tr><td class="label">Pembayaran Hutang (Periode Ini)</td><td class="value green">Rp {{ number_format($totalDebtPaid, 0, ',', '.') }}</td></tr>
            <tr><td class="label">Total Sisa Hutang (Saat Ini)</td><td class="value red">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</td></tr>
        </table>
    </div>

    <!-- SALES RECEIPTS TABLE -->
    @if($salesReceipts->count() > 0)
    <div class="section">
        <div class="section-title">Detail Penjualan ({{ $salesReceipts->count() }} transaksi)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 35%;">Toko</th>
                    <th style="width: 20%;">Total</th>
                    <th style="width: 12%;">Bayar</th>
                    <th style="width: 13%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesReceipts as $i => $receipt)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $receipt->transaction_date ? \Carbon\Carbon::parse($receipt->transaction_date)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $receipt->store->name ?? $receipt->store_name ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($receipt->total_amount, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($receipt->payment_status === 'lunas')
                            <span class="badge badge-green">Lunas</span>
                        @elseif($receipt->payment_status === 'partial')
                            <span class="badge badge-yellow">Partial</span>
                        @else
                            <span class="badge badge-red">Hutang</span>
                        @endif
                    </td>
                    <td class="text-center"><span class="badge badge-blue">{{ ucfirst($receipt->status) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- PURCHASE RECEIPTS TABLE -->
    @if($purchaseReceipts->count() > 0)
    <div class="section">
        <div class="section-title">Detail Pembelian ({{ $purchaseReceipts->count() }} transaksi)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 35%;">Toko</th>
                    <th style="width: 20%;">Total</th>
                    <th style="width: 12%;">Bayar</th>
                    <th style="width: 13%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseReceipts as $receipt)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $receipt->transaction_date ? \Carbon\Carbon::parse($receipt->transaction_date)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $receipt->store->name ?? $receipt->store_name ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($receipt->total_amount, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($receipt->payment_status === 'lunas')
                            <span class="badge badge-green">Lunas</span>
                        @elseif($receipt->payment_status === 'partial')
                            <span class="badge badge-yellow">Partial</span>
                        @else
                            <span class="badge badge-red">Hutang</span>
                        @endif
                    </td>
                    <td class="text-center"><span class="badge badge-blue">{{ ucfirst($receipt->status) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- PAYMENTS TABLE -->
    @if($payments->count() > 0)
    <div class="section">
        <div class="section-title">Riwayat Pembayaran ({{ $payments->count() }} pembayaran)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 25%;">Toko</th>
                    <th style="width: 18%;">Jumlah</th>
                    <th style="width: 15%;">Metode</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : '-' }}</td>
                    <td>{{ $payment->debt->store->name ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}</td>
                    <td>{{ $payment->payment_method ?? '-' }}</td>
                    <td>{{ $payment->notes ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="page-break"></div>

    <!-- INVENTORY SNAPSHOT -->
    <div class="section">
        <div class="section-title">Snapshot Inventaris ({{ $totalProducts }} produk)</div>
        
        <table class="summary-grid" style="margin-bottom: 15px;">
            <tr><td class="label">Total Produk</td><td class="value">{{ $totalProducts }}</td></tr>
            <tr><td class="label">Nilai Inventaris</td><td class="value blue">Rp {{ number_format($totalInventoryValue, 0, ',', '.') }}</td></tr>
            <tr><td class="label">Stok Rendah (≤ 5)</td><td class="value orange">{{ $lowStockProducts->count() }} produk</td></tr>
            <tr><td class="label">Stok Habis</td><td class="value red">{{ $outOfStockProducts->count() }} produk</td></tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 14%;">SKU</th>
                    <th style="width: 25%;">Nama Produk</th>
                    <th style="width: 12%;">Kategori</th>
                    <th style="width: 14%;">Harga Beli</th>
                    <th style="width: 14%;">Harga Jual</th>
                    <th style="width: 7%;">Stok</th>
                    <th style="width: 9%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td><code>{{ $product->sku }}</code></td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($product->buy_price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $product->stock }}</td>
                    <td class="text-center">
                        @if($product->stock == 0)
                            <span class="badge badge-red">Habis</span>
                        @elseif($product->stock <= 5)
                            <span class="badge badge-yellow">Rendah</span>
                        @else
                            <span class="badge badge-green">OK</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        Laporan ini dibuat secara otomatis oleh sistem OCR Inventory pada {{ $generatedAt }}.<br>
        &copy; {{ date('Y') }} {{ $companyName }} — All rights reserved.
    </div>

</body>
</html>
