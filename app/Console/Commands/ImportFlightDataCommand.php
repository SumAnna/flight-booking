<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ImportFlightData;

/**
 * Class ImportFlightDataCommand
 *
 * Command to dispatch a job for importing flight data from a JSON file.
 */
class ImportFlightDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:flight-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import flight data from JSON file';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $filePath = env('FLIGHT_DATA_PATH', 'storage/app/public/flightData.json');

        if (!file_exists($filePath)) {
            $this->error('Flight data file not found.');

            return;
        }

        $fileContents = file_get_contents($filePath);
        $flightData = json_decode($fileContents, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON format.');

            return;
        }

        ImportFlightData::dispatch($flightData);
        $this->info('Flight data import job dispatched.');
    }
}


