<?php

declare(strict_types=1);

namespace Tests\Unit\Utilities;

use App\Utilities\Constants;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ConstantsTest extends TestCase
{
    #[Test]
    public function it_has_correct_value(): void
    {
        $this->assertSame(100000, Constants::MIN_VERIFICATION_CODE);
        $this->assertSame(999999, Constants::MAX_VERIFICATION_CODE);
        $this->assertSame(5, Constants::EXPIRATION_VERIFICATION_CODE_TIME_IN_MINUTES);
    }
}
