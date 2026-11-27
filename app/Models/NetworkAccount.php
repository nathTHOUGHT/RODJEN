<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'username',
    'password',
    'profile',
    'comment',
    'olt_device',
    'pon_port',
    'nap_device',
    'nap_port',
    'onu_name',
    'caller_id',
    'mac_address',
    'remarks',
])]
class NetworkAccount extends Model
{
    /** @var list<string> */
    protected $hidden = ['password'];

    /** @return BelongsTo<Subscription, $this> */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    protected function casts(): array
    {
        return [
            'password' => 'encrypted',
        ];
    }
}
