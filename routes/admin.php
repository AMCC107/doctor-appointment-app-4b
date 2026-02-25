<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\DoctorController;

Route::get('/', function () {
    return view('admin.dashboard');
})->name('dashboard');

// Gestión de roles
Route::resource('roles', RoleController::class)->names('roles');
Route::resource('users', UserController::class)->names('users');
Route::resource('patients', PatientController::class)->names('patients');
Route::resource('doctors', DoctorController::class)->names('doctors');
