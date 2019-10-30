<?php

namespace App\Controller;

use App\Architecture\Interfaces\ActionInterface;
use App\Architecture\Service\PainterService;
use robotomize\Fujes\SearchFacade;
use robotomize\Fujes\SearchFactory;

final class Action implements ActionInterface
{
    public function generate(string $pattern)
    {
        $pattern = ucfirst($pattern);

        $namespace = "App\\Generators\\$pattern\\Handler";
        $class = new $namespace();
        $class->prompt()
        ->design()
        ->report();
    }

    public function advise(string $search)
    {
        $painter = new PainterService();
        $search = str_replace('"', '', $search);

        $searchObject = SearchFactory::find(
            __DIR__.'/../../db.json', //json filename
            $search,                  //search string
            2,                        //Depth into array
            false,                    //output json (or array)
            true,                     //Multiple result
            1,                        //Search quality
            'dev'                     //version
        )->fetchAll();

        if (is_string($searchObject)) {
            echo $painter->color('error', $searchObject)."\n";
        } else {
            echo $painter->color('question', "We found the matching design patterns for '$search': \n\t");
            foreach ($searchObject as $key => $result) {
                $pattern = explode(',',$result[0]);
                echo $painter->color('variable', ucfirst($pattern[0])." pattern: ");
                echo $painter->color('error', ucfirst($pattern[1])."  \n\t");
                echo $painter->color('note', substr($result[1], 0, 300).' ... '."\n\n\t");
            }
            echo "\n";
        }
    }

    public function help()
    {
        echo 'Still working on it';
    }
}
