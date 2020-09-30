<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\BusinessLogic\Interfaces\ImportInterface;

class UpdateProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Database entries based on feed';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ImportInterface $import)
    {
        parent::__construct();
        $this->importer = new $import;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $time_start = microtime(true);
            $this->importer
                ->readFile(config('queries.sql.load_products_data_in_file'), env('TEST_PRODUCT_UPDATE_FILE_PATH', env('CSV_FILE_LOCATION')))
                ->updateRow();
            $time_end = microtime(true);
            printf('Script took: ' . ($time_end - $time_start) . ' seconds to complete');
        } catch (Exception $e) {
            printf($e->getMessage());
            Log::info($e->getMessage());
        }
    }
}
