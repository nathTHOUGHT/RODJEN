<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int|null $plan_id
 * @property Carbon|null $installation_date
 * @property int|null $billing_day
 * @property Carbon|null $subscription_date
 * @property string $credit_limit
 * @property string $service_type
 * @property string $status
 * @property-read Subscriber|null $subscriber
 * @property-read Plan|null $plan
 * @property-read NetworkAccount|null $networkAccount
 */
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
    /** @return BelongsTo<Subscriber, $this> */
    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    /** @return BelongsTo<Plan, $this> */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /** @return HasOne<NetworkAccount, $this> */
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
