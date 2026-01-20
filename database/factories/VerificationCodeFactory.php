<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\VerificationType;
use App\Models\User;
use App\Models\VerificationCode;
use App\Utilities\Constants;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VerificationCode>
 */
final class VerificationCodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();

        return [
            'user_id' => $user->id,
            'type' => fake()->randomElement(VerificationType::cases()),
            'code' => fake()->numberBetween(Constants::MIN_VERIFICATION_CODE, Constants::MAX_VERIFICATION_CODE),
            'expires_at' => now()->addMinutes(Constants::EXPIRATION_VERIFICATION_CODE_TIME_IN_MINUTES),
        ];
    }
}
