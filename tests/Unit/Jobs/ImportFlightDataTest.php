<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ImportFlightData;
use App\Models\Flight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

/**
 * Class ImportFlightDataTest
 *
 * This class tests the ImportFlightData job to ensure it correctly imports flight data into the database.
 */
class ImportFlightDataTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the ImportFlightData job.
     *
     * This test retrieves flight data from a JSON file, runs the ImportFlightData job,
     * and asserts that the number of records in the database matches the expected count.
     *
     */
    public function test_import_flight_data_job()
    {
        $filePath = base_path('storage/app/public/flightData.json');

        $this->assertFileExists($filePath, "The flight data file does not exist at $filePath");

        $flightData = json_decode(file_get_contents($filePath), true);

        $job = new ImportFlightData($flightData);
        $job->handle();

        $this->assertDatabaseCount('flights', count($flightData['data']));
    }
}
