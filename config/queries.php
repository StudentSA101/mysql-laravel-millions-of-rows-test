<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Custom config queries
    |--------------------------------------------------------------------------
    |
    | This file is for storing and accessing
    | long queries created in the application.
    |
    */

    'sql' => [
        'drop_temp_table_if_exists' =>

        "   DROP TABLE IF EXISTS temp_table_products;",

        'create_temp_table' =>

        "   CREATE TABLE temp_table_products
            LIKE products;",

        'load_products_data_in_file' =>

        "   LOAD DATA LOCAL INFILE '%s'
            INTO TABLE temp_table_products
            FIELDS TERMINATED BY ';'
            LINES TERMINATED BY '\n'
            IGNORE 1 ROWS
            (@varianten_id,@dummy,@dummy,@dummy,@produkttitel,@dummy,@dummy,@size,@dummy,@quantity,
            @dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,
            @dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,
            @dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,
            @dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,
            @dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,
            @dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,
            @dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,
            @dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,
            @dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,
            @dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@dummy,
            @dummy,@dummy,@dummy,@dummy,@dummy,@dummy,@preis)
            SET varianten_id=@varianten_id,name=@produkttitel,in_stock=IF(@quantity>0,1,0),price=@preis,size=@size,created_at=CURRENT_TIMESTAMP,updated_at=CURRENT_TIMESTAMP;",

        'create_products_table_data' =>

        "   INSERT INTO products (products.varianten_id,products.name,products.in_stock,products.price,products.size,products.created_at,products.updated_at)
            SELECT temp_table_products.varianten_id,temp_table_products.name,temp_table_products.in_stock,temp_table_products.price,temp_table_products.size,temp_table_products.created_at,temp_table_products.updated_at
            FROM products
            RIGHT JOIN temp_table_products
            USING(varianten_id)
            WHERE products.varianten_id
            IS NULL;",

        'update_products_table_data' =>

        "   UPDATE products
            INNER JOIN temp_table_products
            USING(varianten_id)
            SET products.name = temp_table_products.name, products.in_stock = temp_table_products.in_stock, products.size = temp_table_products.size, products.price = temp_table_products.price, products.updated_at = CURRENT_TIMESTAMP
            WHERE products.name != temp_table_products.name
            OR products.in_stock != temp_table_products.in_stock
            OR products.size != temp_table_products.size
            OR products.price != temp_table_products.price;",

        'delete_products_table_data' =>

        "   DELETE products FROM products
            LEFT JOIN temp_table_products
            USING(varianten_id)
            WHERE temp_table_products.varianten_id IS NULL",

        'load_products_data_in_file_test_500000' =>

        "   LOAD DATA LOCAL INFILE '%s'
            INTO TABLE temp_table_products
            FIELDS TERMINATED BY ';'
            LINES TERMINATED BY '\n'
            IGNORE 1 ROWS
            (@varianten_id,@produkttitel,@size,@quantity,@preis)
            SET varianten_id=@varianten_id,name=@produkttitel,in_stock=IF(@quantity>0,1,0),price=@preis,size=@size,created_at=CURRENT_TIMESTAMP,updated_at=CURRENT_TIMESTAMP;",

    ],

];
