<?php

namespace App\Architecture\Interfaces;

interface SequencesInterface
{
    public function list();

    public function get(string $file);
}
