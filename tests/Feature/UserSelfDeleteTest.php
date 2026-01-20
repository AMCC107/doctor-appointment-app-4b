<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Usamos la cualidad para refrescar DB entre test
uses(RefreshDatabase::class);

test('Un usuario no puede eliminarse a sí mismo', function () {
    
    // 1) Crear un usuario de prueba 
    $user = User::factory()->create();

    // 2) Simulamos que ya inició sesión 
    $this->actingAs($user,'web');

    // 3) Deshabilitar el middleware CSRF para esta prueba
    $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

    //4) Simulamos una petición HTTP DELETE 
    $response = $this->delete(route('admin.users.destroy', $user));

    //5) Esperamos que el servidor bloquee la acción (403 Forbidden)
    $response->assertStatus(403);

    //6) Verificar que el usuario sigue existiendo en BD
    $this->assertDatabaseHas('users', [
        'id' => $user->id]);
});
