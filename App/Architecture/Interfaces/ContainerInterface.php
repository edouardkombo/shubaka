<?php

namespace App\Architecture\Interfaces;

interface ContainerInterface
{
    public function get(string $id);

    public function set(string $key, string $value);

    public function has(string $id);
}
