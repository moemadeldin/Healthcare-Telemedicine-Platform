<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Enums\VerificationType;
use App\Jobs\SendVerificationCodeJob;
use App\Mail\VerificationCodeMail;
use App\Models\User;
use App\Utilities\Constants;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SendVerificationCodeJobTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_verification_code_for_user(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $job = new SendVerificationCodeJob($user);
        $job->handle();

        $this->assertDatabaseHas('verification_codes', [
            'user_id' => $user->id,
            'type' => VerificationType::EMAIL->value,
        ]);
    }

    #[Test]
    public function it_sends_verification_email_to_user(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'first_name' => 'John',
        ]);

        $job = new SendVerificationCodeJob($user);
        $job->handle();

        Mail::assertSent(VerificationCodeMail::class, fn ($mail): mixed => $mail->hasTo($user->email));
    }

    #[Test]
    public function it_generates_six_digit_code(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $job = new SendVerificationCodeJob($user);
        $job->handle();

        $verificationCode = $user->verificationCode()->first();

        $this->assertNotNull($verificationCode);
        $this->assertEquals(6, mb_strlen((string) $verificationCode->code));
        $this->assertIsNumeric($verificationCode->code);
    }

    #[Test]
    public function it_sets_expiration_time_correctly(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $expectedExpiration = now()->addMinutes(Constants::EXPIRATION_VERIFICATION_CODE_TIME_IN_MINUTES);

        $job = new SendVerificationCodeJob($user);
        $job->handle();

        $verificationCode = $user->verificationCode()->first();

        $this->assertNotNull($verificationCode->expires_at);
        $this->assertEquals(
            $expectedExpiration->format('Y-m-d H:i'),
            $verificationCode->expires_at->format('Y-m-d H:i')
        );
    }

    #[Test]
    public function it_passes_correct_data_to_mail(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'first_name' => 'Jane',
        ]);

        $job = new SendVerificationCodeJob($user);
        $job->handle();

        Mail::assertSent(VerificationCodeMail::class, fn ($mail): bool => $mail->firstName === $user->first_name
            && mb_strlen((string) $mail->code) === 6);
    }

    #[Test]
    public function it_throws_exception_on_failure(): void
    {
        Mail::shouldReceive('to')
            ->andThrow(new Exception('Mail server error'));

        $user = User::factory()->create();

        $job = new SendVerificationCodeJob($user);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Mail server error');

        $job->handle();
    }

    #[Test]
    public function it_logs_error_when_sending_fails(): void
    {
        Mail::shouldReceive('to')
            ->andThrow(new Exception('Mail error'));

        $user = User::factory()->create();

        $job = new SendVerificationCodeJob($user);

        Log::spy();

        try {
            $job->handle();
        } catch (Exception) {
            // Expected
        }

        Log::shouldHaveReceived('error')
            ->with('Failed to send verification code', Mockery::type('array'))
            ->once();
    }
}
