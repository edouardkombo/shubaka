<?php

namespace App\Controller;

use App\Architecture\Interfaces\ActionInterface;
use App\Architecture\Interfaces\ContainerInterface;
use robotomize\Fujes\SearchFactory;
use App\Controller\DI;

class Container implements ActionInterface, ContainerInterface
{
    /**
     * @var array
     */
    public $services = [];

    /**
     * @var DI
     */
    public $injector;

    public function __construct()
    {
        $this->injector = new DI();
        $configFilePath = __DIR__ . '/../Config/Services.json';

        //Load all the config params
        if (file_exists($configFilePath)) {
            $configs = json_decode(file_get_contents($configFilePath), true);
            foreach ($configs as $key => $config) {
                $this->set($config['alias'], $config['class']);
            }
        }
    }

    public function get(string $alias)
    {
        if (in_array($alias, array_keys($this->services))) {
            return $this->services[$alias];
        }

        throw new \Exception("Service $alias not found!");
    }

    public function set(string $alias, string $className): self
    {
        $instance = $this->injector->resolve($className);
        if (is_object($instance)) {
            $this->services[$alias] = $instance;
            $this->injector->setInstance($className, $instance);
        }

        return $this;
    }

    public function generate(string $pattern)
    {
        $pattern = ucfirst($pattern);
        $namespace = "App\\Generators\\$pattern\\Interviewer";

        if (!class_exists($namespace)) {
            echo $this->get('painter.service')->color('error', "This design pattern is not supported. Try 'shubaka help' for more details \n");

            return;
        }

        //Instantiate class if needed with required arguments
        $class = $this->injector->resolve($namespace)->prompt()->design()->report();
    }

    public function advise(string $search)
    {
        $search = str_replace('"', '', $search);

        $searchObject = SearchFactory::find(
            __DIR__.'/../Data/db.json', //json filename
            $search,                  //search string
            2,                        //Depth into array
            false,                    //output json (or array)
            true,                     //Multiple result
            1,                        //Search quality
            'dev'                     //version
        )->fetchAll();

        if (is_string($searchObject)) {
            echo $this->get('painter.service')->color('error', $searchObject)."\n";
        } else {
            echo $this->get('painter.service')->color('question', "We found the matching design patterns for '$search': \n\t");
            foreach ($searchObject as $key => $result) {
                $pattern = explode(',', $result[0]);
                echo $this->get('painter.service')->color('variable', ucfirst($pattern[0]).' pattern: ');
                echo $this->get('painter.service')->color('error', ucfirst($pattern[1])."  \n\t");
                echo $this->get('painter.service')->color('note', substr($result[1], 0, 300).' ... '."\n\n\t");
            }
            echo "\n";
        }
    }

    public function help()
    {
        echo $this->get('painter.service')->color('question', "List of all available commands': \n\n\t");

        echo $this->get('painter.service')->color('variable', 'generate    ');
        echo $this->get('painter.service')->color('note', "Generate a custom design pattern suiting your needs\n\t");

        echo $this->get('painter.service')->color('variable', 'advise      ');
        echo $this->get('painter.service')->color('note', "Find the appropriate design pattern you need\n\t");

        echo $this->get('painter.service')->color('variable', 'list        ');
        echo $this->get('painter.service')->color('note', "List all the currently design patterns you can generate\n\n");
    }

    public function list()
    {
        $conventions = array_keys(json_decode(file_get_contents(__DIR__.'/../Sequences/SequenceConvention.json'), true));
        echo $this->get('painter.service')->color('question', "List of all currently supported design patterns': \n\n\t");
        foreach ($conventions as $dp) {
            echo $this->get('painter.service')->color('variable', ucfirst($dp)." => 'shubaka generate $dp'\n\t");
        }
        echo "\n";
    }
}
