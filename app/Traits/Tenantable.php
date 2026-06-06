<?php

namespace App\Traits;

use App\Models\Scopes\TenantScope;
use App\Models\Company;

trait Tenantable
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (auth()->hasUser() && auth()->user()->company_id && !$model->company_id) {
                $model->company_id = auth()->user()->company_id;
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
