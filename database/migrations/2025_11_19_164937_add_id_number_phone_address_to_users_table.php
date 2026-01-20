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
        Schema::table('users', function (Blueprint $table) {
            // Verificar si las columnas ya existen antes de agregarlas (idempotente)
            if (!Schema::hasColumn('users', 'id_number')) {
                $table->string('id_number')->unique()->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('id_number');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable()->after('phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Verificar si las columnas existen antes de eliminarlas (idempotente)
        $columnsToDrop = [];
        
        if (Schema::hasColumn('users', 'id_number')) {
            $columnsToDrop[] = 'id_number';
        }
        if (Schema::hasColumn('users', 'phone')) {
            $columnsToDrop[] = 'phone';
        }
        if (Schema::hasColumn('users', 'address')) {
            $columnsToDrop[] = 'address';
        }
        
        if (!empty($columnsToDrop)) {
            Schema::table('users', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }
    }
};
