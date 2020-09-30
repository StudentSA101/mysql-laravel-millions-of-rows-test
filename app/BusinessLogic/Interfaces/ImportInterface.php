<?php

namespace App\BusinessLogic\Interfaces;

interface ImportInterface
{
    public function readFile(string $query, string $path);

    public function createRow();

    public function deleteRow();

    public function updateRow();
}
