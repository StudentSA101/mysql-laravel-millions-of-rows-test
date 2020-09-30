<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\BusinessLogic\Interfaces\ImportInterface;

class ImportProducts extends Command
{
    /**
     * @var string
     */
    protected $signature = 'import:products';

    /**
     * @var string
     */
    protected $description = 'Imports a merchant\'s products into our database for further processing';

    /**
     * Instance for dealing with file imports
     *
     * @var App\BusinessLogic\ImportInterface
     */
    private $importer;

    /**
     * @return void
     */
    public function __construct(ImportInterface $import)
    {
        parent::__construct();
        $this->importer = new $import;
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        try {
            $time_start = microtime(true);
            $this->importer
                ->readFile(config('queries.sql.load_products_data_in_file'), env('TEST_CSV_FILE_LOCATION'))
                ->createRow()
                ->updateRow()
                ->deleteRow();
            $time_end = microtime(true);
            printf('Script took: ' . ($time_end - $time_start) . ' seconds to complete');
        } catch (Exception $e) {
            printf($e->getMessage());
            Log::info($e->getMessage());
        }
    }
}
