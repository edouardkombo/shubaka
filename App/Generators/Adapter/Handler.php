<?php

namespace App\Generators\Adapter;

use App\Architecture\Abstracts\InterviewAbstract;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;

/**
 * Adapter design pattern
 * 
 * @author Edouard Kombo <edouard.kombo@gmail.com>
 */
final class Handler extends InterviewAbstract
{
  private $className = "";

  public function __construct() 
  {
    $this->className = (new \ReflectionClass($this))->getShortName();
    parent::__construct($this->className);

    $this->enabled = true;
  }

  public function design()
  {
    $baseNamespace = $this->classesBag['namespace'];
    $abtractsNamespace = $this->classesBag['namespace'] . "\\Abstracts";
    $interfacesNamespace = $this->classesBag['namespace'] . "\\Interfaces";

    $fileConcrete = new PhpFile;
    $fileAbstract = new PhpFile;
    $fileConcrete->addComment($this->credits['generated']);
    $fileAbstract->addComment($this->credits['generated']);
    $fileInterfaces = [];

    $namespaceConcrete = $fileConcrete->addNamespace($baseNamespace);
    $namespaceAbstracts = $fileAbstract->addNamespace($abtractsNamespace);
    $namespaceInterfaces = [];

    $concreteKeys = array_keys($this->classesBag['concrete']);

    $concreteClassName = ucfirst(end($concreteKeys));
    $abstractClassName = $concreteClassName . "Abstract";

    //Build abstract class
    $abstract = $namespaceAbstracts->addClass($abstractClassName);
    $abstract->setAbstract();
    $abstract->addComment(sprintf($this->credits['file'], 'Abstract Class'));
    $abstract->addComment($this->credits['author']);

    $abstract->addMethod('__construct')
             ->setBody("//Do Something");

    //Build interfaces
    $interfaces = [];
    foreach ($this->classesBag['interface'] as $c => $methods) {
      $interfaceClassName = ucfirst($c) . "Interface";
      $fileInterfaces[$c] = new PhpFile;
      $fileInterfaces[$c]->addComment($this->credits['generated']);
      $namespaceInterfaces[$c] = $fileInterfaces[$c]->addNamespace($interfacesNamespace);
      $interfaces[$c] = $namespaceInterfaces[$c]->addClass($interfaceClassName);
      $interfaces[$c]->addComment(sprintf($this->credits['file'], 'Interface'));
      $interfaces[$c]->addComment($this->credits['author']);
      $interfaces[$c]->setType('interface');

      $namespaceAbstracts->addUse($interfacesNamespace . "\\" . $interfaceClassName);
      $abstract->addImplement($interfaceClassName);

      foreach ($methods['methods'] as $key => $methodName) {
        $interfaces[$c]->addMethod(ucfirst($methodName));
        
        //Implement methods from interfaces
        $abstract->addMethod(ucfirst($methodName))
        ->setBody('return true;');
      }
    } 

    //Build concrete class
    $concrete = $namespaceConcrete->addClass($concreteClassName);
    $namespaceConcrete->addUse($abtractsNamespace . "\\" . $abstractClassName);
    $concrete->addExtend($abstractClassName);
    $concrete->addComment(sprintf($this->credits['file'], 'Concrete Class'))
             ->addComment($this->credits['author']);
    $concrete->addMethod('__construct')
             ->setBody("parent::__construct();\n //You can override the abstraction class whenever possible");


    // to generate PHP code simply cast to string or use echo:
    $this->generatorBag[$baseNamespace] = [[$concreteClassName => $fileConcrete]];
    $this->generatorBag[$abtractsNamespace] = [[$abstractClassName => $fileAbstract]];
    $this->generatorBag[$interfacesNamespace] = [];

    foreach ($fileInterfaces as $key => $object) {
      array_push($this->generatorBag[$interfacesNamespace], [$key."Interface" => $object]);
    }
  }
}