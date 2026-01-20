<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners;

use App\Events\UserRegistered;
use App\Jobs\SendVerificationCodeJob;
use App\Listeners\SendVerificationCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SendVerificationCodeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_dispatches_send_verification_code_job(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $event = new UserRegistered($user);

        $listener = new SendVerificationCode();
        $listener->handle($event);

        Queue::assertPushed(SendVerificationCodeJob::class, fn ($job): bool => $job->user->id === $user->id);
    }

    #[Test]
    public function it_should_queue_returns_true(): void
    {
        $user = User::factory()->create();
        $event = new UserRegistered($user);

        $listener = new SendVerificationCode();

        $this->assertTrue($listener->shouldQueue($event));
    }

    #[Test]
    public function it_dispatches_job_with_correct_user(): void
    {
        Queue::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'first_name' => 'John',
        ]);

        $event = new UserRegistered($user);
        $listener = new SendVerificationCode();
        $listener->handle($event);

        Queue::assertPushed(SendVerificationCodeJob::class, fn ($job): bool => $job->user->email === 'test@example.com'
            && $job->user->first_name === 'John');
    }

    #[Test]
    public function it_only_dispatches_one_job(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $event = new UserRegistered($user);

        $listener = new SendVerificationCode();
        $listener->handle($event);

        Queue::assertPushed(SendVerificationCodeJob::class, 1);
    }
}
