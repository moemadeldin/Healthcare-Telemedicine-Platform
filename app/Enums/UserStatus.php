<?php

declare(strict_types=1);

namespace App\Enums;

enum UserStatus: string
{
    case VERIFIED = 'verified';
    case NOT_VERIFIED = 'not verified';
    case BLOCKED = 'blocked';
    case PENDING = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::VERIFIED => 'Verified',
            self::NOT_VERIFIED => 'Not Verified',
            self::BLOCKED => 'Blocked',
            self::PENDING => 'Pending',
        };
    }
}
