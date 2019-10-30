<?php

namespace App\Architecture\Service;

use App\Architecture\Interfaces\PainterInterface;

class PainterService implements PainterInterface
{
    /**
     * @var array
     */
    protected $codes = [
        'question' => "\e[32m", 
        'variable' => "\e[36m", 
        'log' => "\e[35m", 
        'note' => "\e[33m",
        'error' => "\e[31m",
        'cancel' => "\033[0m"
    ];

    public function color(string $type, string $input): string
    {
        $input = str_replace(['{{','}}'], [$this->codes['note'], $this->codes['cancel'] . $this->codes['question']], $input);
        $input = $this->codes[$type] . $input . $this->codes['cancel'];
        return ('variable' === $type) ? $input . $this->codes['question'] : $input;
    }
}
