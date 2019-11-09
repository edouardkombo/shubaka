<?php

namespace App\Architecture\Interfaces;

interface SequencesInterface
{
    public function list();

    public function load(string $file);

    public function setPattern(string $pattern);
}
