<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController; // <- Importa tu controlador

Route::get('/', function () {
    return view('admin.dashboard');
})->name('dashboard');

// Gestión de roles
Route::resource('roles', RoleController::class);
