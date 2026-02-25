<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Auditoría módulo doctores: tabla specialties, columnas medical_license y bio,
     * specialty_id obligatorio, FK specialty_id restrictOnDelete.
     */
    public function up(): void
    {
        // 1. Renombrar tabla specialities -> specialties
        if (Schema::hasTable('specialities') && !Schema::hasTable('specialties')) {
            Schema::rename('specialities', 'specialties');
        }

        // 2. Asegurar al menos una especialidad (para specialty_id obligatorio)
        if (! DB::table('specialties')->exists()) {
            DB::table('specialties')->insert([
                'name' => 'Medicina General',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            $this->upSqlite();
        } else {
            $this->upOther();
        }
    }

    protected function upSqlite(): void
    {
        // Asignar especialidad por defecto a doctores sin especialidad
        $firstSpecialtyId = DB::table('specialties')->min('id');
        if ($firstSpecialtyId !== null) {
            DB::table('doctors')->whereNull('speciality_id')->update(['speciality_id' => $firstSpecialtyId]);
        }

        // Recrear tabla doctors con esquema de auditoría y FK restrict
        Schema::create('doctors_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('specialty_id')->constrained('specialties')->restrictOnDelete();
            $table->string('medical_license')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
        });

        DB::statement('
            INSERT INTO doctors_new (id, user_id, specialty_id, medical_license, bio, created_at, updated_at)
            SELECT id, user_id, speciality_id, medical_license_number, biography, created_at, updated_at
            FROM doctors
        ');

        Schema::drop('doctors');
        Schema::rename('doctors_new', 'doctors');
    }

    protected function upOther(): void
    {
        // Asignar especialidad por defecto
        $firstSpecialtyId = DB::table('specialties')->min('id');
        if ($firstSpecialtyId !== null) {
            DB::table('doctors')->whereNull('speciality_id')->update(['speciality_id' => $firstSpecialtyId]);
        }

        Schema::table('doctors', function (Blueprint $table) {
            $table->dropForeign(['speciality_id']);
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->renameColumn('speciality_id', 'specialty_id');
            $table->renameColumn('medical_license_number', 'medical_license');
            $table->renameColumn('biography', 'bio');
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->foreignId('specialty_id')->nullable(false)->change();
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->foreign('specialty_id')->references('id')->on('specialties')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            if (Schema::hasTable('specialties')) {
                Schema::rename('specialties', 'specialities');
            }
            Schema::dropIfExists('doctors');
            Schema::create('doctors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('speciality_id')->nullable()->constrained('specialities')->nullOnDelete();
                $table->string('medical_license_number')->nullable();
                $table->text('biography')->nullable();
                $table->timestamps();
            });
        }
    }
};
