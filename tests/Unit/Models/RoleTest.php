<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RoleTest extends TestCase
{
    use RefreshDatabase;

    private Role $role;

    protected function setUp(): void
    {
        parent::setUp();

        $this->role = Role::factory()->create();

    }

    #[Test]
    public function it_returns_belongs_to_many_relationship(): void
    {
        $relation = $this->role->users();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
    }
}
