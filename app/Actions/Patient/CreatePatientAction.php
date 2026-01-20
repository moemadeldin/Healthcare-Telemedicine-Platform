<?php

declare(strict_types=1);

namespace App\Actions\Patient;

use App\Enums\Roles;
use App\Events\UserRegistered;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final readonly class CreatePatientAction
{
    /**
     * @param  array{first_name: string, last_name: string, email: string, password: string}  $data
     */
    public function execute(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            $user = User::query()->create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make((string) $data['password']),
            ]);

            $user->assignRole(Roles::PATIENT->value);

            $token = $user->createToken('Register Token')->plainTextToken;
            $user->setAttribute('access_token', $token);

            event(new UserRegistered($user));

            return $user;
        });
    }
}
