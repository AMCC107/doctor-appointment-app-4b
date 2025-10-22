<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;

Route::get('/', function () {
    return view('admin.dashboard');
})->name('dashboard');

// Gestión de roles
Route::resource('roles', RoleController::class)->names('roles');
