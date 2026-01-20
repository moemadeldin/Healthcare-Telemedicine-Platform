<?php

declare(strict_types=1);

namespace Tests\Unit\Mail;

use App\Mail\VerificationCodeMail;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class VerificationCodeMailTest extends TestCase
{
    #[Test]
    public function it_can_be_instantiated_with_code_and_name(): void
    {
        $mail = new VerificationCodeMail('123456', 'John');

        $this->assertInstanceOf(VerificationCodeMail::class, $mail);
        $this->assertEquals('123456', $mail->code);
        $this->assertEquals('John', $mail->firstName);
    }

    #[Test]
    public function it_has_correct_subject(): void
    {
        $mail = new VerificationCodeMail('123456', 'John');

        $envelope = $mail->envelope();

        $this->assertEquals('Email Verification Code', $envelope->subject);
    }

    #[Test]
    public function it_uses_correct_view(): void
    {
        $mail = new VerificationCodeMail('123456', 'John');

        $content = $mail->content();

        $this->assertEquals('emails.verification-code', $content->view);
    }

    #[Test]
    public function it_passes_code_to_view(): void
    {
        $mail = new VerificationCodeMail('654321', 'Jane');

        $this->assertEquals('654321', $mail->code);
    }

    #[Test]
    public function it_passes_name_to_view(): void
    {
        $mail = new VerificationCodeMail('123456', 'Alice');

        $this->assertEquals('Alice', $mail->firstName);
    }

    #[Test]
    public function it_renders_view_with_data(): void
    {
        $mail = new VerificationCodeMail('999888', 'Bob');

        $rendered = $mail->render();

        $this->assertStringContainsString('999888', $rendered);
        $this->assertStringContainsString('Bob', $rendered);
    }

    #[Test]
    public function it_can_be_sent_to_user(): void
    {
        Mail::fake();

        $mail = new VerificationCodeMail('123456', 'John');

        Mail::to('test@example.com')->send($mail);

        Mail::assertSent(VerificationCodeMail::class, fn ($sentMail): bool => $sentMail->code === '123456'
            && $sentMail->firstName === 'John');
    }
}
