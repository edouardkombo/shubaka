<?php

namespace App\Architecture\Interfaces;

interface ContainerInterface
{
    public function get(string $alias);

    public function set(string $alias, string $className);
}
