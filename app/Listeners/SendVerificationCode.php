<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Jobs\SendVerificationCodeJob;
use Illuminate\Contracts\Queue\ShouldQueue;

final readonly class SendVerificationCode implements ShouldQueue
{
    public function handle(UserRegistered $event): void
    {
        dispatch(new SendVerificationCodeJob($event->user));
    }

    public function shouldQueue(UserRegistered $event): bool
    {
        return true;
    }
}
