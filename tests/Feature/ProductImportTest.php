<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductImportTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test
     *
     * @return void
     */
    public function testImportProducts()
    {
        putenv("TEST_PRODUCT_CREATE_FILE_PATH=CsvFiles/test_create_products1.csv");
        $this->artisan('create:products')
            ->assertExitCode(0);

        putenv("TEST_PRODUCT_UPDATE_FILE_PATH=CsvFiles/test_update_products1.csv");
        $this->artisan('update:products')
            ->assertExitCode(0);

        putenv("TEST_PRODUCT_DELETE_FILE_PATH=CsvFiles/test_delete_products1.csv");
        $this->artisan('delete:products')
            ->assertExitCode(0);

        // retrieve data from db and assert whether create,update and delete occurred
    }
}
