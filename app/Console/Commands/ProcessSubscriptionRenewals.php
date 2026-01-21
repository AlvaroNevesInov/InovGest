<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessSubscriptionRenewals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:process-renewals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process subscription renewals and scheduled plan changes';

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
        $this->info('Processing subscription renewals...');

        // Get subscriptions that need renewal
        $subscriptionsDue = Subscription::where('status', 'active')
            ->whereNotNull('next_billing_date')
            ->where('next_billing_date', '<=', Carbon::now())
            ->with(['tenant', 'plan', 'scheduledPlan'])
            ->get();

        $renewedCount = 0;
        $failedCount = 0;

        foreach ($subscriptionsDue as $subscription) {
            try {
                // Process renewal (this will also apply scheduled plan changes)
                $this->subscriptionService->renewSubscription($subscription);

                if ($subscription->hasPlanChangeScheduled()) {
                    $this->info("Renewed and changed plan for {$subscription->tenant->name}");
                } else {
                    $this->info("Renewed subscription for {$subscription->tenant->name}");
                }

                $renewedCount++;
            } catch (\Exception $e) {
                $this->error("Failed to renew subscription for {$subscription->tenant->name}: {$e->getMessage()}");

                // Mark subscription as past_due
                $subscription->update(['status' => 'past_due']);
                $failedCount++;
            }
        }

        // Process canceled subscriptions that have ended
        $endedSubscriptions = Subscription::where('status', 'canceled')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', Carbon::now())
            ->get();

        $expiredCount = 0;

        foreach ($endedSubscriptions as $subscription) {
            $subscription->update(['status' => 'expired']);
            $this->info("Expired canceled subscription for {$subscription->tenant->name}");
            $expiredCount++;
        }

        $this->info("Successfully renewed {$renewedCount} subscriptions.");
        $this->info("Failed to renew {$failedCount} subscriptions.");
        $this->info("Expired {$expiredCount} canceled subscriptions.");

        return Command::SUCCESS;
    }
}
