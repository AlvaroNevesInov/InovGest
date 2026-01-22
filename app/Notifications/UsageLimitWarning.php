<?php

namespace App\Notifications;

use App\Models\Subscription;
use App\Models\SubscriptionUsage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UsageLimitWarning extends Notification implements ShouldQueue
{
    use Queueable;

    protected Subscription $subscription;
    protected SubscriptionUsage $usage;
    protected int $threshold;

    /**
     * Create a new notification instance.
     */
    public function __construct(Subscription $subscription, SubscriptionUsage $usage, int $threshold)
    {
        $this->subscription = $subscription;
        $this->usage = $usage;
        $this->threshold = $threshold;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $featureName = $this->getFeatureName($this->usage->feature);
        $percentage = $this->usage->getPercentage();
        $remaining = $this->usage->getRemaining();

        return (new MailMessage)
            ->subject("Aviso: Limite de {$featureName} a {$percentage}%")
            ->greeting("Olá {$notifiable->name},")
            ->line("O seu limite de **{$featureName}** está quase a ser atingido.")
            ->line("**Uso atual:** {$this->usage->used} de {$this->usage->limit} ({$percentage}%)")
            ->line("**Restante:** {$remaining}")
            ->action('Ver Dashboard', route('subscriptions.index'))
            ->line('Considere fazer upgrade do seu plano para evitar interrupções no serviço.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subscription_id' => $this->subscription->id,
            'feature' => $this->usage->feature,
            'used' => $this->usage->used,
            'limit' => $this->usage->limit,
            'percentage' => $this->usage->getPercentage(),
            'threshold' => $this->threshold,
        ];
    }

    /**
     * Get human-readable feature name
     */
    private function getFeatureName(string $feature): string
    {
        return match($feature) {
            'users' => 'Utilizadores',
            'companies' => 'Empresas',
            'storage_gb' => 'Armazenamento',
            'invoices_per_month' => 'Faturas por Mês',
            'proposals_per_month' => 'Propostas por Mês',
            default => ucfirst(str_replace('_', ' ', $feature)),
        };
    }
}
