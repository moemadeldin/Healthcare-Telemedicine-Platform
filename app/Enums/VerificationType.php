<?php

declare(strict_types=1);

namespace App\Enums;

enum VerificationType: string
{
    case EMAIL = 'email';
    case SMS = 'sms';

    public function label(): string
    {
        return match ($this) {
            self::EMAIL => 'Email',
            self::SMS => 'SMS',
        };
    }
}
