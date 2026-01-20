<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\UserStatus;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UserStatusTest extends TestCase
{
    #[Test]
    public function it_has_verified_case(): void
    {
        $this->assertEquals('verified', UserStatus::VERIFIED->value);
    }

    #[Test]
    public function it_has_not_verified_case(): void
    {
        $this->assertEquals('not verified', UserStatus::NOT_VERIFIED->value);
    }

    #[Test]
    public function it_has_blocked_case(): void
    {
        $this->assertEquals('blocked', UserStatus::BLOCKED->value);
    }

    public function it_has_pending_case(): void
    {
        $this->assertEquals('pending', UserStatus::PENDING->value);
    }

    #[Test]
    public function it_returns_correct_label_for_verified(): void
    {
        $this->assertEquals('Verified', UserStatus::VERIFIED->label());
    }

    #[Test]
    public function it_returns_correct_label_for_not_verified(): void
    {
        $this->assertEquals('Not Verified', UserStatus::NOT_VERIFIED->label());
    }

    #[Test]
    public function it_returns_correct_label_for_blocked(): void
    {
        $this->assertEquals('Blocked', UserStatus::BLOCKED->label());
    }

    public function it_returns_correct_label_for_pending(): void
    {
        $this->assertEquals('Pending', UserStatus::PENDING->label());
    }

    #[Test]
    public function it_has_exactly_four_cases(): void
    {
        $cases = UserStatus::cases();

        $this->assertCount(4, $cases);
    }

    #[Test]
    public function it_contains_all_expected_roles(): void
    {
        $cases = UserStatus::cases();
        $values = array_map(fn (UserStatus $case): mixed => $case->value, $cases);

        $this->assertContains('verified', $values);
        $this->assertContains('not verified', $values);
        $this->assertContains('blocked', $values);
        $this->assertContains('pending', $values);
    }
}
