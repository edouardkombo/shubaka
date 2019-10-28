<?php

namespace App\Sequences;

class SequenceLoader 
{
  /** @var array */
  public $sequence;

  /** @var string */
  private $directory;
  
  /** @var array */
  public $list = [];

  public function __construct($sequence) 
  {
    $this->sequence = $sequence;
    $this->directory = __DIR__ . "/$this->sequence/";
  }

  public function list(): self
  {
    if ($handle = opendir($this->directory)) {
      while (false !== ($entry = readdir($handle))) {
          if ($entry != "." && $entry != "..") {
            array_push($this->list, $entry);
          }
      }
      closedir($handle);
    }
    
    return $this;
  }

  public function get(string $file): array
  {
    return json_decode(file_get_contents($this->directory . $file), true);
  }
}