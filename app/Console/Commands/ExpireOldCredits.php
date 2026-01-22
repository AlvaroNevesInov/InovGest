<?php

namespace App\Console\Commands;

use App\Services\CreditService;
use Illuminate\Console\Command;

class ExpireOldCredits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'credits:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire old tenant credits that have passed their expiration date';

    protected CreditService $creditService;

    public function __construct(CreditService $creditService)
    {
        parent::__construct();
        $this->creditService = $creditService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Expiring old credits...');

        $count = $this->creditService->expireOldCredits();

        $this->info("Successfully expired {$count} credits.");

        return Command::SUCCESS;
    }
}
