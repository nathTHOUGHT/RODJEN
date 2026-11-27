<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $profile_group
 * @property string $transaction_type
 * @property int|null $speed_mbps
 * @property string $price
 * @property int $usage_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'profile_group', 'transaction_type', 'speed_mbps', 'price', 'usage_count'])]
class Plan extends Model
{
    /** @var array<string, string> */
    public const PROFILE_GROUPS = [
        'default-encryption' => 'default-encryption',
        'pppoe-45mbit/649' => 'pppoe-45mbit/649',
    ];

    /** @var array<string, string> */
    public const TRANSACTION_TYPES = [
        'mrc' => 'Monthly Recurring Charge (MRC)',
        'otc' => 'One Time Charge (OTC)',
        'pcf' => 'Policy & Condition Fee (PCF)',
        'mrd' => 'Monthly Recurring Discount (MRD)',
        'mds' => 'Monthly Due Surcharge (MDS)',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'speed_mbps' => 'integer',
            'usage_count' => 'integer',
        ];
    }

    public function transactionTypeLabel(): string
    {
        return self::TRANSACTION_TYPES[$this->transaction_type] ?? $this->transaction_type;
    }
}
