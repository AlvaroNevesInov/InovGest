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
        Schema::create('subscription_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->string('feature'); // Ex: 'users', 'companies', 'storage_gb', 'invoices'
            $table->integer('used')->default(0);
            $table->integer('limit')->nullable(); // null = ilimitado
            $table->timestamp('reset_at')->nullable(); // Para limites que resetam (ex: invoices/mês)
            $table->timestamps();

            // Índices
            $table->unique(['subscription_id', 'feature']);
            $table->index('reset_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_usage');
    }
};
