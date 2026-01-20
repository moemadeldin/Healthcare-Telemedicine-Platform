<?php

declare(strict_types=1);

namespace App\Utilities;

final readonly class Constants
{
    public const int MIN_VERIFICATION_CODE = 100_000;

    public const int MAX_VERIFICATION_CODE = 999_999;

    public const int EXPIRATION_VERIFICATION_CODE_TIME_IN_MINUTES = 5;
}
