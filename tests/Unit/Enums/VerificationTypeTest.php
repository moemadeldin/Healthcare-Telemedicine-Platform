<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\VerificationType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class VerificationTypeTest extends TestCase
{
    #[Test]
    public function it_has_email_case(): void
    {
        $this->assertEquals('email', VerificationType::EMAIL->value);
    }

    #[Test]
    public function it_has_sms_case(): void
    {
        $this->assertEquals('sms', VerificationType::SMS->value);
    }

    #[Test]
    public function it_returns_correct_label_for_email(): void
    {
        $this->assertEquals('Email', VerificationType::EMAIL->label());
    }

    public function it_returns_correct_label_for_sms(): void
    {
        $this->assertEquals('SMS', VerificationType::SMS->label());
    }

    #[Test]
    public function it_has_exactly_three_cases(): void
    {
        $cases = VerificationType::cases();

        $this->assertCount(2, $cases);
    }

    #[Test]
    public function it_contains_all_expected_roles(): void
    {
        $cases = VerificationType::cases();
        $values = array_map(fn (VerificationType $case): mixed => $case->value, $cases);

        $this->assertContains('email', $values);
        $this->assertContains('sms', $values);
    }
}
