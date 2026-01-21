<?php

namespace App\Console\Commands;

use App\Mail\TrialEndedMail;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ConvertExpiredTrials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:convert-trials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert expired trial subscriptions to active or expired status';

    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        parent::__construct();
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Converting expired trial subscriptions...');

        // Get trials that have expired
        $expiredTrials = Subscription::where('status', 'trialing')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<=', Carbon::now())
            ->with(['tenant.owner', 'plan'])
            ->get();

        $convertedCount = 0;
        $expiredCount = 0;

        foreach ($expiredTrials as $subscription) {
            try {
                // If plan is free, convert to active
                if ($subscription->plan->isFree()) {
                    $this->subscriptionService->convertTrialToActive($subscription);
                    $this->info("Converted trial to active for {$subscription->tenant->name}");
                    $convertedCount++;
                } else {
                    // For paid plans, mark as expired and send notification
                    $subscription->update([
                        'status' => 'expired',
                        'ends_at' => Carbon::now(),
                    ]);

                    // Send trial ended email
                    /** @var \Illuminate\Contracts\Mail\Mailable $mail */
                    $mail = new TrialEndedMail($subscription, $subscription->tenant);
                    Mail::to($subscription->tenant->owner->email)->send($mail);

                    $this->info("Expired trial for {$subscription->tenant->name} - notification sent");
                    $expiredCount++;
                }
            } catch (\Exception $e) {
                $this->error("Failed to process trial for {$subscription->tenant->name}: {$e->getMessage()}");
            }
        }

        $this->info("Converted {$convertedCount} trials to active.");
        $this->info("Expired {$expiredCount} paid trials.");

        return Command::SUCCESS;
    }
}
