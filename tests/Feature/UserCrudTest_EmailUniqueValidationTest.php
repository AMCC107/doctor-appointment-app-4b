<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

test('update - validation fails when email already belongs to another user', function () {
    // Arrange: Enable exception handling and disable CSRF
    $this->withExceptionHandling();
    $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

    // Create a role (required for validation)
    $role = Role::create(['name' => 'Administrador']);

    // Create User A
    $userA = User::factory()->create([
        'email' => 'usera@example.com',
        'id_number' => 'USER001'
    ]);
    $userA->assignRole($role);

    // Create User B with a different email
    $userB = User::factory()->create([
        'email' => 'userb@example.com',
        'id_number' => 'USER002'
    ]);
    $userB->assignRole($role);

    // Create admin user to perform the update
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'id_number' => 'ADMIN001'
    ]);
    $admin->assignRole($role);

    // Act: Try to update User A using User B's email
    $response = actingAs($admin)
        ->from(route('admin.users.edit', $userA))
        ->put(route('admin.users.update', $userA), [
            'name' => $userA->name,
            'email' => 'userb@example.com', // Email from User B
            'id_number' => $userA->id_number,
            'phone' => $userA->phone,
            'address' => $userA->address,
            'role_id' => $role->id,
        ]);

    // Assert: Validation should fail on email field
    $response->assertSessionHasErrors('email');
});
