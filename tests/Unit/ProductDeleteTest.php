<?php

namespace Tests\Unit;

use Tests\TestCase;

class ProductDeleteTest extends TestCase
{
    public function testProductDelete()
    {
        putenv("TEST_PRODUCT_DELETE_FILE_PATH=CsvFiles/test_delete_products1.csv");
        $this->artisan('delete:products')
            ->assertExitCode(0);

        // retrieve data from db and assert whether delete occured
    }
}
