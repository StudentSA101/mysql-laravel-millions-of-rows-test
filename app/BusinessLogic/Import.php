<?php

namespace App\BusinessLogic;

use App\BusinessLogic\Interfaces\ImportInterface;
use App\Jobs\ProcessProductsCreate;
use App\Jobs\ProcessProductsDelete;
use App\Jobs\ProcessProductsUpdate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDOException;

class Import implements ImportInterface
{

    /**
     * Variable to keep count of number of rows in csv file
     *
     * @var integer
     */
    private $fileRowCount;
    /**
     * Instance of Eloquent containing all the rows to be created
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    private $createRows;
    /**
     * Instance of Eloquent containing all the rows to be updated
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    private $updateRows;
    /**
     * Instance of Eloquent containing all the rows to be deleted
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    private $deleteRows;

    /**
     * Array to keep track of progress
     *
     * @var array
     */
    public $statusArray;

    public function __construct()
    {
        // skipping use of injection as laravel provides facades helpers as abstraction as alternative to dependency injection.
    }
    /**
     * Reads a csv file from storage and streams it into a temporary database table
     *
     * @return App\BusinessLogic\Import
     */
    public function readFile(String $query, String $path)
    {
        try {
            // Could consider running this as a job and add optimising data 3 sec per product section
            // added the jobs to cater for this as alternative if needed
            // set memory_limit for this file to bigger amount for large files
            ini_set('memory_limit', '1024M');
            // Makes queries little bit faster
            DB::connection()->disableQueryLog();
            DB::statement(config('queries.sql.drop_temp_table_if_exists'));
            DB::statement(config('queries.sql.create_temp_table'));
            $this->fileRowCount = DB::connection()->getpdo()->exec(sprintf(
                $query,
                str_replace('\\', '/', Storage::disk('local')->path($path))
            ));
            // For purpose of test just logging to file
            Log::channel('custom')->info('Number of rows uploaded to temp_table_product - ');
            Log::channel('custom')->info($this->fileRowCount);
            Log::channel('custom')->info("\n");
            $this->statusArray['Number of rows uploaded to temp_table_product'] = $this->fileRowCount;

            return $this;
        } catch (PDOException $e) {
            Log::info($e);
        }
    }
    /**
     * Creates all the entries in the main table, inserts all rows on first attempt and only new ones thereafter based on feed
     *
     * @return App\BusinessLogic\Import
     */
    public function createRow()
    {
        try {
            $this->createRows = DB::table('products')
                ->select('temp_table_products.*')
                ->rightJoin('temp_table_products', 'products.varianten_id', '=', 'temp_table_products.varianten_id')
                ->whereNull('products.varianten_id')
                ->get();
            // add optimising data 3 sec per product section
            // Could consider running this as a job
            ProcessProductsCreate::dispatch($this->createRows);
            // For purpose of test just logging to file
            Log::channel('custom')->info('Rows Added - ');
            if ($this->createRows->count() !== $this->fileRowCount) {
                Log::channel('custom')->info($this->createRows->toArray());
                $this->statusArray['Rows Added'] = $this->createRows->toArray();
            } else {
                Log::channel('custom')->info($this->fileRowCount);
                $this->statusArray['Rows Added'] = $this->fileRowCount;
            }
            Log::channel('custom')->info("\n");
            DB::statement(config('queries.sql.create_products_table_data'));

            return $this;
        } catch (PDOException $e) {
            Log::info($e);
        }
    }
    /**
     * Updates existing rows in the main table based feed
     *
     * @return App\BusinessLogic\Import
     */
    public function updateRow()
    {
        try {
            $this->updateRows = DB::table('products')
                ->select('temp_table_products.*')
                ->join('temp_table_products', 'products.varianten_id', '=', 'temp_table_products.varianten_id')
                ->whereRaw('products.name != temp_table_products.name')
                ->orWhereRaw('products.in_stock != temp_table_products.in_stock')
                ->orWhereRaw('products.in_stock != temp_table_products.in_stock')
                ->orWhereRaw('products.size != temp_table_products.size')
                ->orWhereRaw('products.price != temp_table_products.price')
                ->get();
            // add optimising data 3 sec per product section
            // Could consider running this as a job
            ProcessProductsUpdate::dispatch($this->updateRows);
            // For purpose of test just logging to file
            Log::channel('custom')->info('Rows Updated - ');
            Log::channel('custom')->info($this->updateRows->toArray());
            Log::channel('custom')->info("\n");
            $this->statusArray['Rows Updated'] = $this->updateRows->toArray();
            DB::statement(config('queries.sql.update_products_table_data'));

            return $this;
        } catch (PDOException $e) {
            Log::info($e);
        }
    }
    /**
     * Deletes rows based on removed data from feed
     *
     * @return App\BusinessLogic\Import
     */
    public function deleteRow()
    {
        try {
            $this->deleteRows = DB::table('products')
                ->select('products.*')
                ->leftJoin('temp_table_products', 'products.varianten_id', '=', 'temp_table_products.varianten_id')
                ->whereRaw('temp_table_products.varianten_id IS NULL')
                ->get();
            // add optimising data 3 sec per product section
            // Could consider running this as a job
            ProcessProductsDelete::dispatch($this->deleteRows);
            // For purpose of test just logging to file
            Log::channel('custom')->info('Rows Deleted - ');
            Log::channel('custom')->info($this->deleteRows->toArray());
            Log::channel('custom')->info("\n");
            $this->statusArray['Rows Deleted'] = $this->deleteRows->toArray();
            DB::statement(config('queries.sql.delete_products_table_data'));

            return $this;
        } catch (PDOException $e) {
            Log::info($e);
        }
    }
}
