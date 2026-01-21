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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('plans');
            $table->foreignId('previous_plan_id')->nullable()->constrained('plans'); // Para downgrades

            $table->enum('status', ['trialing', 'active', 'past_due', 'canceled', 'expired'])->default('trialing');

            // Trial
            $table->timestamp('trial_ends_at')->nullable();
            $table->boolean('trial_notification_sent')->default(false);

            // Billing
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('next_billing_date')->nullable();

            // Upgrade/Downgrade
            $table->foreignId('scheduled_plan_id')->nullable()->constrained('plans'); // Plano agendado para próximo período
            $table->timestamp('plan_change_scheduled_for')->nullable();
            $table->decimal('prorated_amount', 10, 2)->nullable(); // Valor de pró-rata em upgrades

            // Cancelamento
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('ends_at')->nullable(); // Quando a subscrição termina após cancelamento

            $table->timestamps();

            // Índices
            $table->index(['tenant_id', 'status']);
            $table->index('trial_ends_at');
            $table->index('next_billing_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
