<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'buy_price',
        'sell_price',
        'stock',
    ];

    public function receiptItems()
    {
        return $this->hasMany(ReceiptItem::class);
    }
}
