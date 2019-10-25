<?php

namespace App\Logic\Architecture\Abstracts;

use App\Logic\Architecture\Interfaces\InterviewInterface;
use App\Logic\Sequences\Loader;

/**
 * We extend the contract for all the concrete classes
 */
abstract class InterviewAbstract implements InterviewInterface
{
  public $sequence = [];

  public $bag = [
    'concrete' => [],
    'interface' => [],
    'abstract' => [],
    'trait' => []
  ];

  public function __construct($className)
  {
    $this->sequence = (new Loader($className))->get();
  }

  abstract public function prompt();

  abstract public function design();

  //Same for all generators
  public function report()
  {

  }
}