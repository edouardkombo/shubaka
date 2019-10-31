<?php

namespace App\Controller;

use App\Architecture\Interfaces\ProcessInterface;
use App\Architecture\Service\PainterService;

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

    public function __construct(array $argv, array $systemArgs)
    {
        $this->argv = $argv;

        if (!in_array(strtolower($argv[1]), $systemArgs)) {
            $this->fail("First expected argument must be one of those \n\t".implode(',', $systemArgs)."\n\n");
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
        echo (new PainterService)->color('error', $error);
        die();
    }
}
