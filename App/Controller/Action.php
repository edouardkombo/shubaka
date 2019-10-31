<?php

namespace App\Controller;

use App\Architecture\Interfaces\ActionInterface;
use App\Architecture\Service\PainterService;
use robotomize\Fujes\SearchFacade;
use robotomize\Fujes\SearchFactory;

final class Action implements ActionInterface
{
    /**
     * @var PainterService
     */
    public $painter;

    public function __construct()
    {
        $this->painter = new PainterService();
    }

    public function generate(string $pattern)
    {
        $pattern   = ucfirst($pattern);
        $namespace = "App\\Generators\\$pattern\\Handler";

        if (!class_exists($namespace)) {
            echo $this->painter->color('error', "This design pattern is not supportedsss. Try 'shubaka help' for more details \n");
            return;
        }

        $class = (new $namespace())->prompt()->design()->report();
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
            echo $this->painter->color('error', $searchObject)."\n";
        } else {
            echo $this->painter->color('question', "We found the matching design patterns for '$search': \n\t");
            foreach ($searchObject as $key => $result) {
                $pattern = explode(',',$result[0]);
                echo $this->painter->color('variable', ucfirst($pattern[0])." pattern: ");
                echo $this->painter->color('error', ucfirst($pattern[1])."  \n\t");
                echo $this->painter->color('note', substr($result[1], 0, 300).' ... '."\n\n\t");
            }
            echo "\n";
        }
    }

    public function help()
    {
        echo $this->painter->color('question', "List of all available commands': \n\n\t");

        echo $this->painter->color('variable', "generate    ");
        echo $this->painter->color('note', "Generate a custom design pattern suiting your needs\n\t");

        echo $this->painter->color('variable', "advise      ");
        echo $this->painter->color('note', "Find the appropriate design pattern you need\n\t");

        echo $this->painter->color('variable', "list        ");
        echo $this->painter->color('note', "List all the currently design patterns you can generate\n\n");
    }

    public function list()
    {
        $conventions = array_keys(json_decode(file_get_contents(__DIR__ . "/../Sequences/SequenceConvention.json"), true));
        echo $this->painter->color('question', "List of all currently supported design patterns': \n\n\t");
        foreach ($conventions as $dp) {
            echo $this->painter->color('variable', ucfirst($dp) . " => 'shubaka generate $dp'\n\t");
        }
        echo "\n";
    }
}
