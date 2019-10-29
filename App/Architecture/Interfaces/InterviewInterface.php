<?php

namespace App\Architecture\Interfaces;

/**
 * The contract, this generator has to execute these two methods only
 */
interface InterviewInterface
{
  function prompt();

  function design();

  function report();
}