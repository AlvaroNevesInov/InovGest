<?php

namespace App\Console\Commands;

use App\Mail\TrialEndedMail;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckTrialSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-trials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check trial subscriptions and send notifications for trials ending soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking trial subscriptions...');

        // Get trials ending in 3 days that haven't been notified
        $trialsEndingSoon = Subscription::where('status', 'trialing')
            ->where('trial_notification_sent', false)
            ->whereNotNull('trial_ends_at')
            ->whereBetween('trial_ends_at', [
                Carbon::now(),
                Carbon::now()->addDays(3)
            ])
            ->with(['tenant.owner', 'plan'])
            ->get();

        $count = 0;

        foreach ($trialsEndingSoon as $subscription) {
            try {
                // Send notification email
                /** @var \Illuminate\Contracts\Mail\Mailable $mail */
                $mail = new TrialEndedMail($subscription, $subscription->tenant);
                Mail::to($subscription->tenant->owner->email)->send($mail);

                // Mark as notified
                $subscription->update(['trial_notification_sent' => true]);

                $this->info("Notification sent to {$subscription->tenant->name}");
                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to send notification to {$subscription->tenant->name}: {$e->getMessage()}");
            }
        }

        $this->info("Sent {$count} trial ending notifications.");

        return Command::SUCCESS;
    }
}
