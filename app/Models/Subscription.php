<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'plan_id',
    'installation_date',
    'billing_day',
    'subscription_date',
    'credit_limit',
    'service_type',
    'status',
])]
class Subscription extends Model
{
    use HasFactory;

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function networkAccount(): HasOne
    {
        return $this->hasOne(NetworkAccount::class);
    }

    protected function casts(): array
    {
        return [
            'installation_date' => 'date',
            'billing_day' => 'integer',
            'subscription_date' => 'date',
            'credit_limit' => 'decimal:2',
        ];
    }
}
