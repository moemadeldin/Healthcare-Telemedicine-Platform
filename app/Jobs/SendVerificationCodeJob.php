<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\VerificationType;
use App\Mail\VerificationCodeMail;
use App\Models\User;
use App\Utilities\Constants;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

final class SendVerificationCodeJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public User $user
    ) {}

    public function handle(): void
    {
        try {
            $code = (string) random_int(Constants::MIN_VERIFICATION_CODE, Constants::MAX_VERIFICATION_CODE);

            $this->user->verificationCode()->create([
                'type' => VerificationType::EMAIL->value,
                'code' => $code,
                'expires_at' => now()->addMinutes(Constants::EXPIRATION_VERIFICATION_CODE_TIME_IN_MINUTES),
            ]);
            Mail::to($this->user->email)->send(
                new VerificationCodeMail($code, (string) $this->user->first_name)
            );

        } catch (Exception $exception) {
            Log::error('Failed to send verification code', [
                'user_id' => $this->user->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
