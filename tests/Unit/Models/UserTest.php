<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Enums\Roles;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class UserTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        Role::query()->create(['name' => Roles::ADMIN->value]);
        Role::query()->create(['name' => Roles::DOCTOR->value]);
        Role::query()->create(['name' => Roles::PATIENT->value]);
    }

    #[Test]
    public function test_it_can_assign_a_role_to_user(): void
    {
        $this->user->assignRole(Roles::ADMIN->value);

        $this->assertTrue($this->user->roles()->exists());
        $this->assertEquals(1, $this->user->roles()->count());
        $this->assertEquals(Roles::ADMIN->value, $this->user->roles()->first()->name);
    }

    #[Test]
    public function test_it_cannot_assign_multiple_roles_to_user(): void
    {
        $this->user->assignRole(Roles::ADMIN->value);
        $this->user->assignRole(Roles::DOCTOR->value);

        $this->assertEquals(1, $this->user->roles()->count());
    }

    #[Test]
    public function test_it_does_not_duplicate_roles_when_assigning_same_role_twice(): void
    {
        $this->user->assignRole(Roles::ADMIN->value);
        $this->user->assignRole(Roles::ADMIN->value);

        $this->assertEquals(1, $this->user->roles()->count());
    }

    #[Test]
    public function test_it_throws_exception_when_assigning_non_existent_role(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->user->assignRole('non_existent_role');
    }

    #[Test]
    public function test_it_can_check_if_user_has_a_specific_role(): void
    {
        $this->user->assignRole(Roles::DOCTOR->value);

        $this->assertTrue($this->user->hasRole(Roles::DOCTOR->value));
        $this->assertFalse($this->user->hasRole(Roles::ADMIN->value));
        $this->assertFalse($this->user->hasRole(Roles::PATIENT->value));
    }

    #[Test]
    public function test_it_can_check_if_user_is_admin(): void
    {
        $this->assertFalse($this->user->isAdmin());

        $this->user->assignRole(Roles::ADMIN->value);

        $this->assertTrue($this->user->isAdmin());
    }

    #[Test]
    public function test_it_can_check_if_user_is_doctor(): void
    {
        $this->assertFalse($this->user->isDoctor());

        $this->user->assignRole(Roles::DOCTOR->value);

        $this->assertTrue($this->user->isDoctor());
    }

    #[Test]
    public function test_it_can_check_if_user_is_patient(): void
    {
        $this->assertFalse($this->user->isPatient());

        $this->user->assignRole(Roles::PATIENT->value);

        $this->assertTrue($this->user->isPatient());
    }

    #[Test]
    public function test_it_returns_belongs_to_many_relationship(): void
    {
        $relation = $this->user->roles();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
    }
}
