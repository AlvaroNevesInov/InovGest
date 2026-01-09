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
        // Update contacts table to support encrypted values
        Schema::table('contacts', function (Blueprint $table) {
            $table->text('email')->nullable()->change();
            $table->text('phone')->nullable()->change();
            $table->text('mobile')->nullable()->change();
        });

        // Update entities table to support encrypted values
        Schema::table('entities', function (Blueprint $table) {
            // Drop unique index on nif before changing type
            $table->dropUnique(['nif']);
        });

        Schema::table('entities', function (Blueprint $table) {
            $table->text('email')->nullable()->change();
            $table->text('phone')->nullable()->change();
            $table->text('mobile')->nullable()->change();
            $table->string('nif', 500)->nullable()->change();
        });

        Schema::table('entities', function (Blueprint $table) {
            // Recreate unique index on nif with limited length
            $table->unique('nif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('mobile')->nullable()->change();
        });

        Schema::table('entities', function (Blueprint $table) {
            $table->dropUnique(['nif']);
        });

        Schema::table('entities', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('mobile')->nullable()->change();
            $table->string('nif')->nullable()->change();
        });

        Schema::table('entities', function (Blueprint $table) {
            $table->unique('nif');
        });
    }
};
