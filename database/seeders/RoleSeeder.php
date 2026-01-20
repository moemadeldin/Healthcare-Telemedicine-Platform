<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\Role;
use Illuminate\Database\Seeder;

final class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::query()->create(['name' => Roles::ADMIN->value]);
        Role::query()->create(['name' => Roles::PATIENT->value]);
        Role::query()->create(['name' => Roles::DOCTOR->value]);
    }
}
