<?php

namespace App\Architecture\Interfaces;

/**
 * The contract, this generator has to execute these two methods only.
 */
interface InterviewerInterface
{
    public function prompt();

    public function design();

    public function report();
}
