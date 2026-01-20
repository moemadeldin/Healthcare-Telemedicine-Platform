<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\VerificationCode;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class VerificationCodeTest extends TestCase
{
    use RefreshDatabase;

    private VerificationCode $code;

    protected function setUp(): void
    {
        parent::setUp();

        $this->code = VerificationCode::factory()->create();

    }

    #[Test]
    public function it_returns_belongs_to_relationship(): void
    {
        $relation = $this->code->user();

        $this->assertInstanceOf(BelongsTo::class, $relation);
    }
}
