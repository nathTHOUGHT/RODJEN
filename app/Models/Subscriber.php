<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'account_number',
    'first_name',
    'last_name',
    'landmark',
    'billing_address',
    'contact_number',
    'email',
    'coordinates',
    'remarks',
    'area',
    'external_url',
    'image_url',
])]
class Subscriber extends Model
{
    use HasFactory;

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    public function fullName(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }
}
