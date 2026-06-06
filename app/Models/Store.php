<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Tenantable;

class Store extends Model
{
    use Tenantable;

    protected $fillable = [
        'name',
        'address',
        'phone',
    ];

    public function debts()
    {
        return $this->hasMany(Debt::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }
}
