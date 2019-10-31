<?php

namespace App\Architecture\Interfaces;

interface ActionInterface
{
    public function generate(string $pattern);

    public function advise(string $search);

    public function help();

    public function list();
}
