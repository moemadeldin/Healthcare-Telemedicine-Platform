<?php

declare(strict_types=1);

namespace Tests\Feature\Patient;

use App\Enums\Roles;
use App\Events\UserRegistered;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RegisterPatientControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::query()->create(['name' => Roles::PATIENT->value]);
        Role::query()->create(['name' => Roles::DOCTOR->value]);
        Role::query()->create(['name' => Roles::ADMIN->value]);
    }

    #[Test]
    public function it_creates_patient_with_valid_data(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/v1/auth/register/patients', $data);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'access_token',
                ],
            ])
            ->assertJson([
                'status' => 'Success',
                'message' => 'Patient Created Successfuly, Check your mail for verification.',
            ]);

        $this->assertDatabaseHas('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
        ]);
    }

    #[Test]
    public function it_hashes_password_before_saving(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->postJson('/api/v1/auth/register/patients', $data);

        $user = User::query()->where('email', 'john@gmail.com')->first();

        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    #[Test]
    public function it_assigns_patient_role_to_user(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->postJson('/api/v1/auth/register/patients', $data);

        $user = User::query()->where('email', 'john@gmail.com')->first();

        $this->assertTrue($user->isPatient());
        $this->assertFalse($user->isDoctor());
        $this->assertFalse($user->isAdmin());
    }

    #[Test]
    public function it_generates_access_token_for_user(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/v1/auth/register/patients', $data);

        $response->assertJsonStructure([
            'data' => ['access_token'],
        ]);

        $token = $response->json('data.access_token');

        $this->assertNotNull($token);
        $this->assertIsString($token);
    }

    #[Test]
    public function it_dispatches_user_registered_event(): void
    {
        Event::fake();

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->postJson('/api/v1/auth/register/patients', $data);

        Event::assertDispatched(UserRegistered::class, fn ($event): bool => $event->user->email === 'john@gmail.com');
    }

    #[Test]
    public function it_requires_all_mandatory_fields(): void
    {
        $response = $this->postJson('/api/v1/auth/register/patients', []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'first_name',
                'last_name',
                'email',
                'password',
            ]);
    }

    #[Test]
    public function it_requires_first_name(): void
    {
        $data = [
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/v1/auth/register/patients', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['first_name']);
    }

    #[Test]
    public function it_requires_last_name(): void
    {
        $data = [
            'first_name' => 'John',
            'email' => 'john@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/v1/auth/register/patients', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['last_name']);
    }

    #[Test]
    public function it_requires_valid_email_format(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/v1/auth/register/patients', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function it_requires_unique_email(): void
    {
        User::factory()->create(['email' => 'existing@gmail.com']);

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'existing@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/v1/auth/register/patients', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function it_requires_password_confirmation(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/v1/auth/register/patients', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['password']);
    }

    #[Test]
    public function it_requires_password_and_confirmation_to_match(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password',
        ];

        $response = $this->postJson('/api/v1/auth/register/patients', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['password']);
    }

    #[Test]
    public function it_requires_password_minimum_length(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ];

        $response = $this->postJson('/api/v1/auth/register/patients', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['password']);
    }

    #[Test]
    public function it_requires_password_maximum_length(): void
    {
        $longPassword = str_repeat('a', 89);

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => $longPassword,
            'password_confirmation' => $longPassword,
        ];

        $response = $this->postJson('/api/v1/auth/register/patients', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['password']);
    }

    #[Test]
    public function it_requires_first_name_maximum_length(): void
    {
        $data = [
            'first_name' => str_repeat('a', 256),
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/v1/auth/register/patients', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['first_name']);
    }

    // ===================================
    // EDGE CASES
    // ===================================

    #[Test]
    public function it_trims_whitespace_from_names(): void
    {
        $data = [
            'first_name' => '  John  ',
            'last_name' => '  Doe  ',
            'email' => 'john@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->postJson('/api/v1/auth/register/patients', $data);

        $user = User::query()->where('email', 'john@gmail.com')->first();

        $this->assertEquals('John', $user->first_name);
        $this->assertEquals('Doe', $user->last_name);
    }

    #[Test]
    public function it_only_creates_one_user_per_request(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->postJson('/api/v1/auth/register/patients', $data);

        $this->assertEquals(1, User::query()->count());
    }

    #[Test]
    public function it_does_not_expose_password_in_response(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/v1/auth/register/patients', $data);

        $response->assertJsonMissing(['password']);
        $response->assertDontSee('password123');
    }
}
