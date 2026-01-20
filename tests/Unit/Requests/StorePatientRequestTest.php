<?php

declare(strict_types=1);

namespace Tests\Unit\Requests;

use App\Http\Requests\Patient\StorePatientRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class StorePatientRequestTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_passes_validation_with_valid_data(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = $this->validateData($data);

        $this->assertTrue($validator->passes());
        $this->assertEmpty($validator->errors()->all());
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

        $validator = $this->validateData($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('first_name', $validator->errors()->toArray());
    }

    #[Test]
    public function it_requires_first_name_to_be_string(): void
    {
        $data = [
            'first_name' => 12345,
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = $this->validateData($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('first_name', $validator->errors()->toArray());
    }

    #[Test]
    public function it_requires_first_name_maximum_255_characters(): void
    {
        $data = [
            'first_name' => str_repeat('a', 256),
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = $this->validateData($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('first_name', $validator->errors()->toArray());
    }

    #[Test]
    public function it_accepts_first_name_with_255_characters(): void
    {
        $data = [
            'first_name' => str_repeat('a', 255),
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = $this->validateData($data);

        $this->assertTrue($validator->passes());
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

        $validator = $this->validateData($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('last_name', $validator->errors()->toArray());
    }

    #[Test]
    public function it_requires_last_name_to_be_string(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => ['Doe'],
            'email' => 'john@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = $this->validateData($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('last_name', $validator->errors()->toArray());
    }

    #[Test]
    public function it_requires_last_name_maximum_255_characters(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => str_repeat('a', 256),
            'email' => 'john@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = $this->validateData($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('last_name', $validator->errors()->toArray());
    }

    #[Test]
    public function it_requires_email(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = $this->validateData($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    #[Test]
    public function it_requires_valid_email_format(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = $this->validateData($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    #[Test]
    public function it_accepts_valid_email_formats(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = $this->validateData($data);

        $this->assertTrue($validator->passes());
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

        $validator = $this->validateData($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    #[Test]
    public function it_requires_password(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
        ];

        $validator = $this->validateData($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    #[Test]
    public function it_requires_password_to_be_string(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => 12345678,
            'password_confirmation' => 12345678,
        ];

        $validator = $this->validateData($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
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

        $validator = $this->validateData($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
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

        $validator = $this->validateData($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    #[Test]
    public function it_requires_password_minimum_8_characters(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ];

        $validator = $this->validateData($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    #[Test]
    public function it_accepts_password_with_8_characters(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];

        $validator = $this->validateData($data);

        $this->assertTrue($validator->passes());
    }

    #[Test]
    public function it_requires_password_maximum_88_characters(): void
    {
        $longPassword = str_repeat('a', 89);

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => $longPassword,
            'password_confirmation' => $longPassword,
        ];

        $validator = $this->validateData($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    #[Test]
    public function it_accepts_password_with_88_characters(): void
    {
        $password = str_repeat('a', 88);

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@gmail.com',
            'password' => $password,
            'password_confirmation' => $password,
        ];

        $validator = $this->validateData($data);

        $this->assertTrue($validator->passes());
    }

    #[Test]
    public function it_returns_all_validation_errors_for_invalid_data(): void
    {
        $data = [];

        $validator = $this->validateData($data);

        $this->assertFalse($validator->passes());

        $errors = $validator->errors()->toArray();

        $this->assertArrayHasKey('first_name', $errors);
        $this->assertArrayHasKey('last_name', $errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('password', $errors);
    }

    #[Test]
    public function it_rejects_email_with_unicode_characters(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'tëst@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = $this->validateData($data);

        $this->assertTrue($validator->passes());
    }

    #[Test]
    public function it_rejects_empty_strings(): void
    {
        $data = [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ];

        $validator = $this->validateData($data);

        $this->assertFalse($validator->passes());

        $errors = $validator->errors()->toArray();

        $this->assertArrayHasKey('first_name', $errors);
        $this->assertArrayHasKey('last_name', $errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('password', $errors);
    }

    #[Test]
    public function it_rejects_names_with_special_characters(): void
    {
        $data = [
            'first_name' => "O'Brien",
            'last_name' => 'Müller-Schmidt',
            'email' => 'test@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = $this->validateData($data);

        $this->assertFalse($validator->passes());
    }

    private function validateData(array $data): \Illuminate\Validation\Validator
    {
        $request = new StorePatientRequest();

        return Validator::make($data, $request->rules());
    }
}
