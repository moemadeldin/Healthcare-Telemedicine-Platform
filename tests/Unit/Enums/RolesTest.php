<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\Roles;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RolesTest extends TestCase
{
    #[Test]
    public function it_has_admin_case(): void
    {
        $this->assertEquals('admin', Roles::ADMIN->value);
    }

    #[Test]
    public function it_has_patient_case(): void
    {
        $this->assertEquals('patient', Roles::PATIENT->value);
    }

    #[Test]
    public function it_has_doctor_case(): void
    {
        $this->assertEquals('doctor', Roles::DOCTOR->value);
    }

    #[Test]
    public function it_returns_correct_label_for_admin(): void
    {
        $this->assertEquals('Admin', Roles::ADMIN->label());
    }

    #[Test]
    public function it_returns_correct_label_for_patient(): void
    {
        $this->assertEquals('Patient', Roles::PATIENT->label());
    }

    #[Test]
    public function it_returns_correct_label_for_doctor(): void
    {
        $this->assertEquals('Doctor', Roles::DOCTOR->label());
    }

    #[Test]
    public function it_has_exactly_three_cases(): void
    {
        $cases = Roles::cases();

        $this->assertCount(3, $cases);
    }

    #[Test]
    public function it_contains_all_expected_roles(): void
    {
        $cases = Roles::cases();
        $values = array_map(fn (Roles $case): mixed => $case->value, $cases);

        $this->assertContains('admin', $values);
        $this->assertContains('patient', $values);
        $this->assertContains('doctor', $values);
    }
}
