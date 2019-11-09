<?php

namespace App\Architecture\Interfaces;

interface ListenerInterface
{
    public function listen(array $cliArgs);
}
