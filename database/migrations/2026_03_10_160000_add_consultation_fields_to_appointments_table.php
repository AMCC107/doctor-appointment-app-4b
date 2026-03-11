<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->text('diagnosis')->nullable()->after('reason');
            $table->text('treatment')->nullable()->after('diagnosis');
            $table->text('notes')->nullable()->after('treatment');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['diagnosis', 'treatment', 'notes']);
        });
    }
};
