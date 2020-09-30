<?php

namespace Tests\Unit;

use Tests\TestCase;

class ProductCreateTest extends TestCase
{
    public function testProductCreate()
    {
        putenv("TEST_PRODUCT_CREATE_FILE_PATH=CsvFiles/test_create_products1.csv");
        $this->artisan('create:products')
            ->assertExitCode(0);

        // retrieve data from db and assert whether creation occured
    }
}
