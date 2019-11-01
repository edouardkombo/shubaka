<?php

namespace App\Controller;

use App\Architecture\Interfaces\AppInterface;
use App\Controller\ServiceContainer;
use App\Controller\Process;
use App\Controller\Action;

class Index implements AppInterface
{
    /**
     * @var ServiceContainer
     */
    public $container;

    /**
     * @var Process
     */
    private $process;

    /**
     * @var Action
     */
    private $action;

    public function __construct(ServiceContainer $serviceContainer, Process $process, Action $action)
    {
        $this->container = $serviceContainer;
        $this->process   = $process;
        $this->action    = $action;
    }

    public function run()
    {
        $this->process->parse();   
        $this->action->{$this->process->method}($this->process->argument);
    }
}
