<?php
namespace App\Generators\Observer;

use App\Architecture\Abstracts\InterviewAbstract;

use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\PhpLiteral;

/**
 * Observer design pattern
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
    $subjectNamespace = $this->classesBag['namespace'] . "\\Subject";
    $observerNamespace = $this->classesBag['namespace'] . "\\Observer";
    $fileController = new PhpFile;
    $fileSubject = new PhpFile;
    $fileController->addComment($this->credits['generated']);
    $fileSubject->addComment($this->credits['generated']);
    $fileObservers = [];
    $namespaceController = $fileController->addNamespace($baseNamespace);
    $namespaceSubject = $fileSubject->addNamespace($subjectNamespace);
    $namespaceObservers = [];
    $controllerKeys = array_keys($this->classesBag['controller']);
    $subjectKeys = array_keys($this->classesBag['subject']);
    $controllerClassName = ucfirst(end($controllerKeys));
    $subjectClassName = ucfirst(end($subjectKeys));
    
    //Build subject class
    $subject = $namespaceSubject->addClass($subjectClassName);
    $subject->addComment(sprintf($this->credits['file'], 'Subject Class' . "\n"));
    $subject->addComment("The subject owns some important state and notifies observers when the state changes \n");
    $subject->addComment($this->credits['author']);
    $subject->addImplement('SplSubject');
    $subject->addProperty('state')
            ->addComment("@var int For the sake of simplicity, the Subject's state, essential to")
            ->addComment("all subscribers, is stored in this variable.");
    $subject->addProperty('observers')
            ->addComment("@var \SplObjectStorage List of subscribers. In real life, the list of")
            ->addComment("subscribers can be stored more comprehensively (categorized by event")
            ->addComment("type, etc.).");
    $subject->addMethod('__construct')
            ->setBody('$this->observers = new \SplObjectStorage');
    
    $subject->addMethod('attach')
            ->addComment('The subscription management methods')
            ->setBody('
                echo "Subject: Attached an observer."' . "\n" .
                '$this->observers->attach($observer);')
            ->addParameter('observer')->setTypeHint('SplObserver');
    $subject->addMethod('detach')
            ->setBody('$this->observers->detach($observer);'  . "\n" . 'echo "Subject: Detached an observer.";')
            ->addParameter('observer')->setTypeHint('SplObserver');
    $subject->addMethod('notify')
            ->addComment('Triggers an update in each subscriber')
            ->setBody('
                echo "Subject: Notifying observers...\n";' . "\n\n" .
                'foreach ($this->observers as $observer) {' . "\n" .
                "\t" . '$observer->update($this);'  . "\n" .
                '}');
    $subject->addMethod('someBusinessLogic')
            ->addComment('Usually, the subscription logic is only a fraction of what a Subject can')
            ->addComment('really do. Subjects commonly hold some important business logic, that')
            ->addComment('triggers a notification method whenever something important is about to')
            ->addComment('happen (or after it).')
            ->setBody(
              'echo "Subject: I am doing something important.";'  . "\n" .
              '$this->state = rand(0, 10);'   . "\n\n" .
              'echo "Subject: My state has just changed to: {$this->state}"' . "\n" . 
              '$this->notify();'
            );
    //Build observer class
    $observers = [];
    foreach ($this->classesBag['observer'] as $className => $methods) {
      $className = ucfirst($className);
      //We add observers namespaces to the controller
      $namespaceController->addUse($observerNamespace . "\\" . $className);
      $fileObservers[$className] = new PhpFile;
      $namespaceObservers[$className] = $fileObservers[$className]->addNamespace($observerNamespace);
      $observers[$className] = $namespaceObservers[$className]->addClass($className);
      $observers[$className]->addComment(sprintf($this->credits['file'], 'Observer Class' . "\n"));
      $observers[$className]->addComment("Concrete Observers react to the updates issued by the Subject they had been attached to \n");
      $observers[$className]->addComment($this->credits['author']);
      $observers[$className]->addImplement('SplObserver');
      
      $observers[$className]->addMethod('update')
              ->addComment('The subscription management methods')
              ->setBody(
                'if ($subject->state < 3) {'   . "\n" .
                "\t" . 'echo "'.$className.': Reacted to the event.";'   . "\n" .
                '}')
              ->addParameter('subject')->setTypeHint('SplSubject');
    }
    //Build controller class
    $controller = $namespaceController->addClass($controllerClassName);
    $controller->addComment(sprintf($this->credits['file'], 'Controller Class' . "\n"));
    $controller->addComment("Orchestrates the design pattern \n");
    $controller->addComment($this->credits['author']);
    $namespaceController->addUse($subjectNamespace . "\\" . $subjectClassName);
    $controllerBody = '';
    
    foreach ($fileObservers as $key => $object) {
      $controllerBody .= '$'.$key.' = new '. $key . ';' . "\n";
      $controllerBody .= '$subject->attach($'.$key.');' . "\n\n";
    }
      $fileObserversKeys = array_keys($fileObservers);
      $lastFileObserverKey = end($fileObserversKeys);
      $controller->addMethod('__construct')
        ->addComment(sprintf($this->credits['file'], 'Subject Class' . "\n"))
        ->addComment("Orchestrate the event observer \n")
        ->addComment($this->credits['author'])
        ->setBody(
          '$subject = new '. $subjectClassName   . "\n\n" .
           $controllerBody .
          '$subject->someBusinessLogic();' . "\n" .
          '$subject->someBusinessLogic();' . "\n\n" .
          '$subject->detach($'.$lastFileObserverKey.');' . "\n\n" .
          '$subject->someBusinessLogic();'
        );
    //To generate PHP code simply cast to string or use echo:
    $this->generatorBag[$baseNamespace] = [[$controllerClassName => $fileController]];
    $this->generatorBag[$subjectNamespace] = [[$subjectClassName => $fileSubject]];
    $this->generatorBag[$observerNamespace] = [];
    foreach ($fileObservers as $key => $object) {
      array_push($this->generatorBag[$observerNamespace], [$key => $object]);
    }
  }

}