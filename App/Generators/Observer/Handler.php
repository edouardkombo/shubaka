<?php

namespace App\Generators\Observer;

use App\Architecture\Abstracts\InterviewAbstract;
use App\Controller\Index as BaseController;
use Nette\PhpGenerator\PhpFile;

/**
 * Observer design pattern.
 *
 * @author Edouard Kombo <edouard.kombo@gmail.com>
 */
final class Handler extends InterviewAbstract
{
    private $className = '';

    public function __construct(BaseController $container)
    {
        parent::__construct(__NAMESPACE__, $container);
    }

    public function design(): self
    {
        $controllerClass          = new PhpFile();
        $controllerClasses        = array_keys($this->classesBag['controller']);
        $controllerClassName      = ucfirst(end($controllerClasses));
        $controllerClass->addComment($this->credits['generated']);
        $controllerClassNamespace = $controllerClass->addNamespace($this->classesBag['namespace']['general']);

        $subjectClass          = new PhpFile();
        $subjectClasses        = array_keys($this->classesBag['subject']);
        $subjectClassName      = ucfirst(end($subjectClasses));
        $subjectClass->addComment($this->credits['generated']);
        $subjectClassNamespace = $subjectClass->addNamespace($this->classesBag['namespace']['subject']);
        $subject               = $subjectClassNamespace->addClass($subjectClassName);
        $subject->addComment(sprintf($this->credits['file'], 'Subject Class'."\n"));
        $subject->addComment("The subject owns some important state and notifies observers when the state changes \n");
        $subject->addComment($this->credits['author']);
        $subject->addImplement('SplSubject');
        $subject->addProperty('state')
            ->addComment("@var int For the sake of simplicity, the Subject's state, essential to")
            ->addComment('all subscribers, is stored in this variable.');
        $subject->addProperty('observers')
            ->addComment("@var \SplObjectStorage List of subscribers. In real life, the list of")
            ->addComment('subscribers can be stored more comprehensively (categorized by event')
            ->addComment('type, etc.).');
        $subject->addMethod('__construct')
            ->setBody('$this->observers = new \SplObjectStorage;');

        $subject->addMethod('attach')
            ->addComment('The subscription management methods')
            ->setBody('
                echo "Subject: Attached an observer.";'."\n".
                '$this->observers->attach($observer);')
            ->addParameter('observer')->setTypeHint('SplObserver');
        $subject->addMethod('detach')
            ->setBody('$this->observers->detach($observer);'."\n".'echo "Subject: Detached an observer.";')
            ->addParameter('observer')->setTypeHint('SplObserver');
        $subject->addMethod('notify')
            ->addComment('Triggers an update in each subscriber')
            ->setBody('
                echo "Subject: Notifying observers...\n";'."\n\n".
                'foreach ($this->observers as $observer) {'."\n".
                "\t".'$observer->update($this);'."\n".
                '}');
        $subject->addMethod('someBusinessLogic')
            ->addComment('Usually, the subscription logic is only a fraction of what a Subject can')
            ->addComment('really do. Subjects commonly hold some important business logic, that')
            ->addComment('triggers a notification method whenever something important is about to')
            ->addComment('happen (or after it).')
            ->setBody(
              'echo "Subject: I am doing something important.";'."\n".
              '$this->state = rand(0, 10);'."\n\n".
              'echo "Subject: My state has just changed to: {$this->state}";'."\n".
              '$this->notify();'
            );


        //Build observer class
        $observerClasses          = [];
        $observerClassesNamespace = [];
        $observers = [];
        foreach ($this->classesBag['observer'] as $className => $methods) {
            $className = ucfirst($className);
            //We add observers namespaces to the controller
            $controllerClassNamespace->addUse($this->classesBag['namespace']['observer'].'\\'.$className);
            $observerClasses[$className] = new PhpFile();
            $observerClassesNamespace[$className] = $observerClasses[$className]->addNamespace($this->classesBag['namespace']['observer']);
            $observers[$className] = $observerClassesNamespace[$className]->addClass($className);
            $observers[$className]->addComment(sprintf($this->credits['file'], 'Observer Class'."\n"));
            $observers[$className]->addComment("Concrete Observers react to the updates issued by the Subject they had been attached to \n");
            $observers[$className]->addComment($this->credits['author']);
            $observers[$className]->addImplement('SplObserver');
            $observers[$className]->addMethod('update')
              ->addComment('The subscription management methods')
              ->setBody(
                'if ($subject->state < 3) {'."\n".
                "\t".'echo "'.$className.': Reacted to the event.";'."\n".
                '}')
              ->addParameter('subject')->setTypeHint('SplSubject');
        }


        //Build controller class
        $controller = $controllerClassNamespace->addClass($controllerClassName);
        $controller->addComment(sprintf($this->credits['file'], 'Controller Class'."\n"));
        $controller->addComment("Orchestrates the design pattern \n");
        $controller->addComment($this->credits['author']);
        $controllerClassNamespace->addUse($this->classesBag['namespace']['subject'].'\\'.$subjectClassName);
        $controllerBody = '';
        foreach ($observerClasses as $key => $object) {
            $controllerBody .= '$'.$key.' = new '.$key.'();'."\n";
            $controllerBody .= '$subject->attach($'.$key.');'."\n\n";
        }

        $observerClassesKeys = array_keys($observerClasses);
        $lastFileObserverKey = end($observerClassesKeys);
        $controller->addMethod('__construct')
        ->addComment(sprintf($this->credits['file'], 'Subject Class'."\n"))
        ->addComment("Orchestrate the event observer \n")
        ->addComment($this->credits['author'])
        ->setBody(
          '$subject = new '.$subjectClassName."();\n\n".
           $controllerBody.
          '$subject->someBusinessLogic();'."\n".
          '$subject->someBusinessLogic();'."\n\n".
          '$subject->detach($'.$lastFileObserverKey.');'."\n\n".
          '$subject->someBusinessLogic();'
        );

        // Put the classes definition into the generator bag from InterviewAbstract
        $this->generatorBag[$this->classesBag['namespace']['general']] = [[$controllerClassName => $controllerClass]];
        $this->generatorBag[$this->classesBag['namespace']['subject']] = [[$subjectClassName => $subjectClass]];
        $this->generatorBag[$this->classesBag['namespace']['observer']] = [];
        foreach ($observerClasses as $key => $object) {
            array_push($this->generatorBag[$this->classesBag['namespace']['observer']], [$key => $object]);
        }

        return $this;
    }
}
