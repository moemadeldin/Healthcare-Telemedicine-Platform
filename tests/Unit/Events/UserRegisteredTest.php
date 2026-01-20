<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Events\UserRegistered;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class UserRegisteredTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_be_instantiated_with_user(): void
    {
        $user = User::factory()->create();

        $event = new UserRegistered($user);

        $this->assertInstanceOf(UserRegistered::class, $event);
        $this->assertSame($user->id, $event->user->id);
    }

    #[Test]
    public function it_has_correct_user_property(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'first_name' => 'John',
        ]);

        $event = new UserRegistered($user);

        $this->assertEquals('test@example.com', $event->user->email);
        $this->assertEquals('John', $event->user->first_name);
    }
}
