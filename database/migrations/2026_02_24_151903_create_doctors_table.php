<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            
            // Relación 1 a 1 con el Usuario. 
            // cascadeOnDelete() asegura que si se borra la cuenta de usuario, se borre su perfil de doctor.
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Relación con el catálogo de Especialidades. 
            // nullable() permite que se cree sin ella temporalmente, y nullOnDelete() evita que se borre el doctor si borran la especialidad del catálogo.
            $table->foreignId('speciality_id')->nullable()->constrained('specialities')->nullOnDelete();
            
            // Campos de información del doctor (pueden ser nulos inicialmente)
            $table->string('medical_license_number')->nullable();
            $table->text('biography')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};