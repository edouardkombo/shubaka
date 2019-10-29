<?php

namespace App\Sequences;

class SequenceLoader
{
    /** 
     * @var string 
     */
    private $directory;

    /** 
     * @var array 
     */
    public $list = [];

    public function __construct(string $sequence)
    {
        $this->directory = __DIR__."/$sequence/";
    }

    public function list(): self
    {
        if ($handle = opendir($this->directory)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != '.' && $entry != '..') {
                    array_push($this->list, $entry);
                }
            }
            closedir($handle);
        }

        sort($this->list);
        return $this;
    }

    public function get(string $file): array
    {
        return json_decode(file_get_contents($this->directory.$file), true);
    }
}
