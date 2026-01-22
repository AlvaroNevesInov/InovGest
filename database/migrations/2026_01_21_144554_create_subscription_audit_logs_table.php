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
        Schema::create('subscription_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            $table->string('event'); // 'created', 'upgraded', 'downgraded', 'canceled', 'resumed', 'renewed', 'trial_started', 'trial_converted', 'trial_expired'

            $table->foreignId('previous_plan_id')->nullable()->constrained('plans')->onDelete('set null');
            $table->foreignId('new_plan_id')->nullable()->constrained('plans')->onDelete('set null');

            $table->string('previous_status')->nullable();
            $table->string('new_status')->nullable();

            $table->decimal('amount', 10, 2)->nullable(); // Valor do upgrade/downgrade/reembolso
            $table->decimal('prorated_amount', 10, 2)->nullable(); // Valor pró-rata
            $table->decimal('credit_amount', 10, 2)->nullable(); // Crédito gerado (em cancelamentos)

            $table->text('description')->nullable(); // Descrição detalhada da mudança
            $table->json('metadata')->nullable(); // Dados adicionais (ex: dias restantes, razão de cancelamento)

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            // Índices para consultas rápidas
            $table->index(['subscription_id', 'created_at']);
            $table->index(['tenant_id', 'created_at']);
            $table->index('event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_audit_logs');
    }
};
