<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

test('create - validation fails when id_number contains non-alphanumeric characters', function () {
    // Arrange: Enable exception handling and disable CSRF
    $this->withExceptionHandling();
    $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

    // Create a role (required for validation)
    $role = Role::create(['name' => 'Administrador']);

    // Create an authenticated admin user with role
    $admin = User::factory()->create([
        'email' => 'admin@test.com',
        'id_number' => 'ADMIN001'
    ]);
    $admin->assignRole($role);

    // Act: Try to create a user with invalid id_number (contains hyphen)
    $response = actingAs($admin)
        ->from(route('admin.users.create'))
        ->post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'id_number' => 'ABC-123', // Invalid: contains hyphen
            'phone' => '1234567890',
            'address' => 'Test Address 123',
            'role_id' => $role->id,
        ]);

    // Assert: Validation should fail on id_number field
    $response->assertSessionHasErrors('id_number');
});
