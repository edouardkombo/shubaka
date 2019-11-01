<?php

namespace App\Controller;

use App\Architecture\Interfaces\ProcessInterface;
use App\Controller\ServiceContainer;

final class Process implements ProcessInterface
{
    /**
     * @var array
     */
    private $argv;

    /**
     * @var string
     */
    public $method = '';

    /**
     * @var string
     */
    public $argument = '';

    /**
     * @var array
     */
    private $systemArgs = ['advise', 'generate', 'help', 'list'];

    public function __construct(array $argv, ServiceContainer $serviceContainer)
    {
        $this->argv = $argv;
        $this->painter = $serviceContainer->get('painter.service');

        if (!in_array(strtolower($argv[1]), $this->systemArgs)) {
            $this->fail("First expected argument must be one of those \n\t".implode(',', $this->systemArgs)."\n\n");
        }
    }

    public function parse(): self
    {
        $this->method = strtolower($this->argv[1]);
        switch (strtolower($this->argv[1])) {
            case 'help':
                break;
            case 'list':
                break;
            default:
                if (empty($this->argv[2])) {
                    $this->fail('A second argument is expected for "'.$this->argv[1].'" Run "shubaka help" for more details.' . "\n");
                } else {
                    $this->argument = $this->argv[2];
                }
        }

        return $this;
    }

    public function fail(string $error)
    {
        echo $this->painter->color('error', $error);
        die();
    }
}
