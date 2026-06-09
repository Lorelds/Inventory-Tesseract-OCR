<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Tenantable;

class Product extends Model
{
    use Tenantable;

    protected $fillable = [
        'name',
        'sku',
        'category',
        'buy_price',
        'sell_price',
        'stock',
        'image_path',
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
