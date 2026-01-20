<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Patient;

use App\Actions\Patient\CreatePatientAction;
use App\Enums\Roles;
use App\Events\UserRegistered;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CreatePatientActionTest extends TestCase
{
    use RefreshDatabase;

    private CreatePatientAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        Role::query()->create(['name' => Roles::PATIENT->value]);

        $this->action = new CreatePatientAction();
    }

    #[Test]
    public function it_creates_user_with_correct_data(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $user = $this->action->execute($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John', $user->first_name);
        $this->assertEquals('Doe', $user->last_name);
        $this->assertEquals('john@example.com', $user->email);
    }

    #[Test]
    public function it_hashes_password(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $user = $this->action->execute($data);

        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    #[Test]
    public function it_assigns_patient_role(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $user = $this->action->execute($data);

        $this->assertTrue($user->isPatient());
        $this->assertCount(1, $user->roles);
    }

    #[Test]
    public function it_creates_access_token(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $user = $this->action->execute($data);

        $this->assertNotNull($user->access_token);
        $this->assertIsString($user->access_token);
    }

    #[Test]
    public function it_dispatches_user_registered_event(): void
    {
        Event::fake();

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $user = $this->action->execute($data);

        Event::assertDispatched(UserRegistered::class, fn ($event): bool => $event->user->id === $user->id);
    }

    #[Test]
    public function it_wraps_execution_in_database_transaction(): void
    {
        Role::query()->where('name', Roles::PATIENT->value)->delete();

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        try {
            $this->action->execute($data);
        } catch (Exception) {
        }

        $this->assertEquals(0, User::query()->count());
    }

    #[Test]
    public function it_saves_user_to_database(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $user = $this->action->execute($data);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ]);
    }
}
