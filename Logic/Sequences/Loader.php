<?php

namespace App\Logic\Sequences;

class Loader 
{
  public $sequence;

  public function __construct($sequence) 
  {
    $this->sequence = $sequence;
  }

  public function get()
  {
    return json_decode(file_get_contents(__DIR__ . "/$this->sequence.json"), true);
  }
}