<?php

namespace App\Mail;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlanChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;
    public $tenant;
    public $oldPlan;
    public $newPlan;
    public $isUpgrade;

    /**
     * Create a new message instance.
     */
    public function __construct(Subscription $subscription, Tenant $tenant, Plan $oldPlan, Plan $newPlan)
    {
        $this->subscription = $subscription;
        $this->tenant = $tenant;
        $this->oldPlan = $oldPlan;
        $this->newPlan = $newPlan;
        $this->isUpgrade = $newPlan->isUpgradeFrom($oldPlan);
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        $subject = $this->isUpgrade
            ? 'Upgrade realizado com sucesso - ' . $this->newPlan->name
            : 'Alteração de plano agendada - ' . $this->newPlan->name;

        return $this->subject($subject)
                    ->view('emails.plan-changed')
                    ->with([
                        'subscription' => $this->subscription,
                        'tenant' => $this->tenant,
                        'oldPlan' => $this->oldPlan,
                        'newPlan' => $this->newPlan,
                        'isUpgrade' => $this->isUpgrade,
                    ]);
    }
}
