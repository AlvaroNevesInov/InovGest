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
        Schema::create('subscription_usage_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');

            $table->string('feature'); // 'users', 'companies', 'storage_gb', 'invoices', etc.
            $table->integer('used'); // Valor usado naquele momento
            $table->integer('limit')->nullable(); // Limite naquele momento
            $table->decimal('percentage', 5, 2)->nullable(); // Percentagem de uso

            $table->date('recorded_date'); // Data do snapshot
            $table->timestamps();

            // Índices para consultas de histórico
            $table->unique(['subscription_id', 'feature', 'recorded_date'], 'sub_usage_hist_unique');
            $table->index(['tenant_id', 'recorded_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_usage_history');
    }
};
