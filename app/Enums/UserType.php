<?php

namespace App\Enums;

enum UserType: string
{
    case Developer = 'developer';
    case Superadmin = 'superadmin';
    case Admin = 'admin';

    /** @return list<self> */
    public function assignableTypes(): array
    {
        return match ($this) {
            self::Developer => self::cases(),
            self::Superadmin => [self::Superadmin, self::Admin],
            self::Admin => [],
        };
    }

    public function canManageStaff(): bool
    {
        return $this !== self::Admin;
    }

    public function canViewNetworkSecrets(): bool
    {
        return $this !== self::Admin;
    }
}
