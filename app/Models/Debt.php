<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Tenantable;

class Debt extends Model
{
    use Tenantable;

    protected $fillable = [
        'receipt_id',
        'store_id',
        'amount',
        'paid_amount',
        'status',
        'notes',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }

    public function payments()
    {
        return $this->hasMany(DebtPayment::class);
    }
}
