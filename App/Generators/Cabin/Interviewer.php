<?php

namespace App\Generators\Cabin;

use App\Architecture\Abstracts\InterviewerAbstract;
use Nette\PhpGenerator\PhpFile;

/**
 * final Abstraction of Interfaces.
 *
 * @author Edouard Kombo <edouard.kombo@gmail.com>
 */
final class Interviewer extends InterviewerAbstract
{
    public function __construct()
    {
        parent::__construct(__NAMESPACE__);
    }

    public function design(): self
    {
        $finalClass          = new PhpFile();
        $finalClasses        = array_keys($this->classesBag['final']);
        $finalClassName      = ucfirst(end($finalClasses));
        $finalClass->addComment($this->credits['generated']);
        $finalClassNamespace = $finalClass->addNamespace($this->classesBag['namespace']['general']);

        $abstractClass          = new PhpFile();
        $abstractClassName      = $finalClassName.'Abstract';
        $abstractClass->addComment($this->credits['generated']);
        $abstractClassNamespace = $abstractClass->addNamespace($this->classesBag['namespace']['abstract']);
        $abstract               = $abstractClassNamespace->addClass($abstractClassName);
        $abstract->setAbstract();
        $abstract->addComment(sprintf($this->credits['file'], 'Abstract Class'));
        $abstract->addComment($this->credits['author']);
        $abstract->addMethod('__construct')
             ->setBody('//Do Something');        

        //Build interfaces
        $interfaces = [];
        $interfaceClasses = [];
        $interfaceClassesNamespace = [];
        foreach ($this->classesBag['interface'] as $c => $methods) {
            $interfaceClassName = ucfirst($c).'Interface';
            $interfaceClasses[$c] = new PhpFile();
            $interfaceClasses[$c]->addComment($this->credits['generated']);
            $interfaceClassesNamespace[$c] = $interfaceClasses[$c]->addNamespace($this->classesBag['namespace']['interface']);
            $interfaces[$c] = $interfaceClassesNamespace[$c]->addClass($interfaceClassName);
            $interfaces[$c]->addComment(sprintf($this->credits['file'], 'Interface'));
            $interfaces[$c]->addComment($this->credits['author']);
            $interfaces[$c]->setType('interface');

            $abstractClassNamespace->addUse($this->classesBag['namespace']['interface'].'\\'.$interfaceClassName);
            $abstract->addImplement($interfaceClassName);

            foreach ($methods['methods'] as $key => $methodName) {
                $interfaces[$c]->addMethod($methodName);

                //Implement interface methods with behavior
                $abstract->addMethod($methodName)
                    ->setBody('return true;');
            }
        }

        //Build final class
        $final = $finalClassNamespace->addClass($finalClassName);
        $finalClassNamespace->addUse($this->classesBag['namespace']['abstract'].'\\'.$abstractClassName);
        $final->addExtend($abstractClassName);
        $final->addComment(sprintf($this->credits['file'], 'final Class'))
             ->addComment($this->credits['author']);
        $final->addMethod('__construct')
             ->setBody("parent::__construct();\n //You can override the abstraction class whenever possible");

        // Put the classes definition into the generator bag from InterviewerAbstract
        $this->generatorBag[$this->classesBag['namespace']['general']] = [[$finalClassName => $finalClass]];
        $this->generatorBag[$this->classesBag['namespace']['abstract']] = [[$abstractClassName => $abstractClass]];
        $this->generatorBag[$this->classesBag['namespace']['interface']] = [];

        foreach ($interfaceClasses as $key => $object) {
            array_push($this->generatorBag[$this->classesBag['namespace']['interface']], [$key.'Interface' => $object]);
        }

        return $this;
    }
}
