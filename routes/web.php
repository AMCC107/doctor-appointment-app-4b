<?php

use Illuminate\Support\Facades\Route;
Route::redirect('/','/admin');

//Route::get('/', function () {
//    return view('welcome');
//});

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorAvailabilityController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\DoctorController as AdminDoctorController;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard', ['title' => 'Dashboard']); // Placeholder title assuming admin dashboard exists
    })->name('dashboard');

    // Módulo de Citas Manuales (Admin)nuevo de nuevo
    Route::resource('appointments', AdminAppointmentController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::get('appointments/{appointment}/consultation', [AdminAppointmentController::class, 'consultation'])->name('appointments.consultation');

    // Horarios del doctor
    Route::get('doctors/{doctor}/schedules', [AdminDoctorController::class, 'schedules'])->name('doctors.schedules');
    Route::post('doctors/{doctor}/schedules', [AdminDoctorController::class, 'storeSchedules'])->name('doctors.schedules.store');
});

// Rutas genéricas que estaban antes y que faltan para que no rompa el Sidebar
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('doctor-availabilities/edit', [App\Http\Controllers\DoctorAvailabilityController::class, 'edit'])->name('doctor_availabilities.edit');
    Route::post('doctor-availabilities/update', [App\Http\Controllers\DoctorAvailabilityController::class, 'update'])->name('doctor_availabilities.update');
    Route::post('doctor-availabilities/slots', [App\Http\Controllers\DoctorAvailabilityController::class, 'getAvailableSlots'])->name('doctor_availabilities.slots');
});
