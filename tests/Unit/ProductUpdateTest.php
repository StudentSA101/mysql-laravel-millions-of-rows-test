<?php

namespace Tests\Unit;

use Tests\TestCase;

class ProductUpdateTest extends TestCase
{
    public function testProductUpdate()
    {
        putenv("TEST_PRODUCT_UPDATE_FILE_PATH=CsvFiles/test_update_products1.csv");
        $this->artisan('update:products')
            ->assertExitCode(0);

        // retrieve data from db and assert whether update occured
    }
}
