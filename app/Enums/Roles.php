<?php

declare(strict_types=1);

namespace App\Enums;

enum Roles: string
{
    case ADMIN = 'admin';
    case PATIENT = 'patient';
    case DOCTOR = 'doctor';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::PATIENT => 'Patient',
            self::DOCTOR => 'Doctor',
        };
    }
}
