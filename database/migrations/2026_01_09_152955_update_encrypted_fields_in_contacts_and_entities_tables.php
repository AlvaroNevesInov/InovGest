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
            $table->text('email')->nullable()->change();
            $table->text('phone')->nullable()->change();
            $table->text('mobile')->nullable()->change();
            // Keep nif as string to maintain unique constraint (it's public data)
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
            $table->string('email')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('mobile')->nullable()->change();
        });
    }
};
