<?php

namespace App\Console\Commands;

use App\Repositories\Eloquent\ExchangeRateRepository;
use App\Services\ExchangeRateProxy;
use App\Services\ExchangeRateService;
use Exception;
use Illuminate\Console\Command;

class ExchangeRateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "get:exchange";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Fetch latest exchange rates";


    /**
     * Execute the console command.
     *
     * @param ExchangeRateService $exchangeRateService
     */
    public function handle(ExchangeRateService $exchangeRateService)
    {
        try {
            $exchangeRateService->run();
            $this->info("exchange rates imported successfully");
        } catch (Exception $e) {
            echo $e->getMessage();
            $this->error("error occurred on getting exchange rates");
        }
    }
}
