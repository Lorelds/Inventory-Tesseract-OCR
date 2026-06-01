<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'category',
        'buy_price',
        'sell_price',
        'stock',
    ];

    public function receiptItems()
    {
        return $this->hasMany(ReceiptItem::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
