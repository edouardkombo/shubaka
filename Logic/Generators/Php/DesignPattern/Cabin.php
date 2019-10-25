<?php

namespace App\Logic\Generators\Php\DesignPattern;

use App\Logic\Architecture\Abstracts\InterviewAbstract;
use Seld\CliPrompt\CliPrompt;
use Nette\PhpGenerator\ClassType;

/**
 * Concrete Abstraction of Interfaces
 * 
 * @author Edouard Kombo <edouard.kombo@gmail.com>
 */
class Cabin extends InterviewAbstract
{
  protected $object = "";
  protected $contracts = [];

  public function __construct() 
  {
    parent::__construct((new \ReflectionClass($this))->getShortName());

    /*$options = ['single_object', '' ];

    echo 'Do you want to generate a design pattern (1) or a simple object (2)? (1/2): ';

    $answer = Seld\CliPrompt\CliPrompt::prompt();

    echo 'You answered: '.$answer . PHP_EOL;


    $class = new Nette\PhpGenerator\ClassType('Demo');

    $class
      ->setFinal()
      ->setExtends('ParentClass')
      ->addImplement('Countable')
      ->addTrait('Nette\SmartObject')
      ->addComment("Description of class.\nSecond line\n")
      ->addComment('@property-read Nette\Forms\Form $form');

    // to generate PHP code simply cast to string or use echo:
    echo $class;
    */
  }

  public function prompt()
  {
    foreach ($this->sequence as $key => $object) {

      foreach ($object as $k => $question) {
        var_dump($k);
        echo $question;
        $answer = CliPrompt::prompt();
        
        if (strpos($k, '_') !== false) {
          if (!in_array($answer, array_keys($this->bag[$k]))) {
            array_push($this->bag[$k], [$answer => ['methods' => []]]);
          }
        
        } else {
          $parts = explode('_', $k);

          if ('loop' !== $parts[0]) {
            $methods = explode(',', $question);
            array_push($this->bag[$k][count($this->bag[$k])-1]['methods'], $methods);
          
          } else {
            //If loop, we do another foreach
          }
        }
        //var_dump($question);
      }
    }
  }

  public function design()
  {

  }
}