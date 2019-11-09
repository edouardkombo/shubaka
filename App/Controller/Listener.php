<?php

namespace App\Controller;

use App\Architecture\Interfaces\ListenerInterface;
use App\Controller\Container;

class Listener implements ListenerInterface
{
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
    private $allowedArgs = ['advise', 'generate', 'help', 'list'];

    public function __construct(Container $container)
    {   
        $this->painter = $container->get('painter.service');
    }

    public function listen(array $cliArgs): self
    {
        if (!in_array(strtolower($cliArgs[1]), $this->allowedArgs)) {
            $this->painter->color("error", "First expected argument must be one of those \n\t".implode(',', $this->allowedArgs)."\n\n");
        }

        $this->method = strtolower($cliArgs[1]);
        switch (strtolower($cliArgs[1])) {
            case 'help':
                break;
            case 'list':
                break;
            default:
                if (empty($cliArgs[2])) {
                    $this->painter->color('error', 'A second argument is expected for "'.$cliArgs[1].'" Run "shubaka help" for more details.' . "\n");
                } else {
                    $this->argument = $cliArgs[2];
                }
        }

        return $this;
    }
}
