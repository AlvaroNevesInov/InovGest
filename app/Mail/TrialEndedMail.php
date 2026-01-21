<?php

namespace App\Mail;

use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrialEndedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;
    public $tenant;

    /**
     * Create a new message instance.
     */
    public function __construct(Subscription $subscription, Tenant $tenant)
    {
        $this->subscription = $subscription;
        $this->tenant = $tenant;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('O seu período de teste terminou - Escolha um plano')
                    ->view('emails.trial-ended')
                    ->with([
                        'subscription' => $this->subscription,
                        'tenant' => $this->tenant,
                        'plan' => $this->subscription->plan,
                    ]);
    }
}
