<?php

namespace App\Architecture\Interfaces;

interface ProcessInterface
{
    public function parse();

    public function fail(string $error);
}
