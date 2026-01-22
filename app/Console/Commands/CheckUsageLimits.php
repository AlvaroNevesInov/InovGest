<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\SubscriptionUsage;
use App\Notifications\UsageLimitWarning;
use Illuminate\Console\Command;

class CheckUsageLimits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-limits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check subscription usage limits and notify tenants approaching their limits';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking usage limits for active subscriptions...');

        // Get all active subscriptions
        /** @var \Illuminate\Database\Eloquent\Collection<int, Subscription> $subscriptions */
        $subscriptions = Subscription::whereIn('status', ['active', 'trialing'])
            ->with(['usage', 'tenant.owner'])
            ->get();

        $notificationsSent = 0;

        /** @var Subscription $subscription */
        foreach ($subscriptions as $subscription) {
            /** @var SubscriptionUsage $usage */
            foreach ($subscription->usage as $usage) {
                // Skip if no limit or unlimited
                if (!$usage->limit) {
                    continue;
                }

                $percentage = $usage->getPercentage();

                // Notify at 80%, 90%, and 95% thresholds
                $shouldNotify = false;
                $threshold = null;

                if ($percentage >= 95 && !$this->wasNotifiedRecently($usage, 95)) {
                    $shouldNotify = true;
                    $threshold = 95;
                } elseif ($percentage >= 90 && !$this->wasNotifiedRecently($usage, 90)) {
                    $shouldNotify = true;
                    $threshold = 90;
                } elseif ($percentage >= 80 && !$this->wasNotifiedRecently($usage, 80)) {
                    $shouldNotify = true;
                    $threshold = 80;
                }

                if ($shouldNotify && $subscription->tenant->owner) {
                    try {
                        $subscription->tenant->owner->notify(
                            new UsageLimitWarning($subscription, $usage, $threshold)
                        );

                        $this->recordNotification($usage, $threshold);
                        $notificationsSent++;

                        $this->info("Notified tenant {$subscription->tenant->name} about {$usage->feature} usage at {$percentage}%");
                    } catch (\Exception $e) {
                        $this->error("Failed to notify tenant {$subscription->tenant->id}: {$e->getMessage()}");
                    }
                }
            }
        }

        $this->info("Successfully sent {$notificationsSent} usage limit warnings.");

        return Command::SUCCESS;
    }

    /**
     * Check if notification was sent recently for this threshold
     *
     * @param SubscriptionUsage $usage
     * @param int $threshold
     * @return bool
     */
    private function wasNotifiedRecently(SubscriptionUsage $usage, int $threshold): bool
    {
        $cacheKey = "usage_limit_notification_{$usage->id}_{$threshold}";
        return cache()->has($cacheKey);
    }

    /**
     * Record that notification was sent
     *
     * @param SubscriptionUsage $usage
     * @param int $threshold
     * @return void
     */
    private function recordNotification(SubscriptionUsage $usage, int $threshold): void
    {
        $cacheKey = "usage_limit_notification_{$usage->id}_{$threshold}";
        // Cache for 24 hours to avoid spamming
        cache()->put($cacheKey, true, now()->addDay());
    }
}
