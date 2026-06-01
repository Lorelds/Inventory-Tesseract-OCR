<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptItem extends Model
{
    protected $fillable = [
        'receipt_id',
        'product_id',
        'product_name',
        'quantity',
        'measure',
        'unit_price',
        'subtotal',
    ];
}
