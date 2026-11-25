<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $title
 * @property string|null $body
 * @property CarbonImmutable $starts_on
 * @property string $repeat
 * @property string $author_name
 */
#[Fillable(['title', 'body', 'starts_on', 'repeat', 'author_name'])]
class StaffReminder extends Model
{
    public const REPEAT_OPTIONS = ['none', 'daily', 'weekly', 'monthly'];

    /** @return Attribute<CarbonImmutable, never> */
    protected function startsOn(): Attribute
    {
        return Attribute::get(fn (string $date): CarbonImmutable => CarbonImmutable::parse($date))->shouldCache();
    }

    public function occursOn(CarbonImmutable $date): bool
    {
        if ($date->lt($this->starts_on)) {
            return false;
        }

        return match ($this->repeat) {
            'daily' => true,
            'weekly' => $date->dayOfWeek === $this->starts_on->dayOfWeek,
            'monthly' => $date->day === $this->starts_on->day,
            default => $date->isSameDay($this->starts_on),
        };
    }
}
