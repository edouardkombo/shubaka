<?php

namespace App\Controller;

use App\Architecture\Interfaces\AppInterface;

use App\Controller\Process;
use App\Controller\Action;

final class Index implements AppInterface
{
    /**
     * @var array
     */
    public $argv;

    /**
     * @var array
     */
    private $systemArgs = ['advise', 'generate', 'help'];

    public function __construct(array $argv)
    {
        $this->argv = $argv;
    }

    public function run()
    {
        $process = new Process($this->argv, $this->systemArgs);
        $action  = new Action();
        
        $process->parse();
        
        $action->{$process->method}($process->argument);
    }
}
