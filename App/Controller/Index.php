<?php

namespace App\Controller;

use App\Architecture\Interfaces\AppInterface;
use App\Controller\Container;
use App\Controller\Listener;

class Index implements AppInterface
{
    public function get(string $alias)
    {
        return (new Container())->get($alias);
    }

    public function run(array $argv): self
    {
        $container = new Container();
        $listener = (new Listener($container))->listen($argv);   
        $container->{$listener->method}($listener->argument);

        return $this;
    }
}
