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
        Schema::create('tenant_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('audit_log_id')->nullable()->constrained('subscription_audit_logs')->onDelete('set null');

            $table->decimal('amount', 10, 2); // Valor do crédito (pode ser negativo para débitos)
            $table->decimal('balance_before', 10, 2)->default(0); // Saldo antes da transação
            $table->decimal('balance_after', 10, 2); // Saldo após a transação

            $table->string('type'); // 'refund', 'cancellation_credit', 'promotional', 'adjustment', 'usage'
            $table->string('status')->default('pending'); // 'pending', 'applied', 'expired', 'void'

            $table->text('description');
            $table->json('metadata')->nullable();

            $table->timestamp('expires_at')->nullable(); // Créditos podem expirar
            $table->timestamp('applied_at')->nullable(); // Quando foi usado
            $table->timestamps();

            // Índices
            $table->index(['tenant_id', 'status']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_credits');
    }
};
