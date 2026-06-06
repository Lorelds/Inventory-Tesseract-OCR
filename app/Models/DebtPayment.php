<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Tenantable;

class DebtPayment extends Model
{
    use Tenantable;

    protected $fillable = [
        'debt_id',
        'amount_paid',
        'payment_date',
        'payment_method',
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }
}
