<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

test('create - validation fails when password confirmation does not match', function () {
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

    // Act: Try to create a user with mismatched passwords
    $response = actingAs($admin)
        ->from(route('admin.users.create'))
        ->post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'otra_cosa', // Does not match
            'id_number' => 'TEST001',
            'phone' => '1234567890',
            'address' => 'Test Address 123',
            'role_id' => $role->id,
        ]);

    // Assert: Validation should fail on password field
    $response->assertSessionHasErrors('password');
});
