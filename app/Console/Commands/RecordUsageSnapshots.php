<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\SubscriptionUsageHistory;
use Illuminate\Console\Command;

class RecordUsageSnapshots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:record-usage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Record daily snapshots of subscription usage for all active subscriptions';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Recording usage snapshots for active subscriptions...');

        $subscriptions = Subscription::whereIn('status', ['active', 'trialing'])
            ->with('usage')
            ->get();

        $count = 0;

        foreach ($subscriptions as $subscription) {
            try {
                SubscriptionUsageHistory::recordSnapshot($subscription);
                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to record snapshot for subscription {$subscription->id}: {$e->getMessage()}");
            }
        }

        $this->info("Successfully recorded {$count} usage snapshots.");

        return Command::SUCCESS;
    }
}
