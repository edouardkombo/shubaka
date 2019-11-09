<?php

namespace App\Architecture\Service;

use App\Architecture\Interfaces\TranslateInterface;
use App\Architecture\Service\PainterService;

class TranslateService implements TranslateInterface
{
    /**
     * @var array
     */
    protected $matches = [];

    /**
     * @var array
     */
    protected $translations = [];

    /**
     * @var array
     */
    protected $bag = [];

    /**
     * @var string
     */
    protected $input;

    public function identify(): self
    {
        preg_match_all('/\__(.*?)\__/s', $this->input, $matches);
        $this->matches = $matches;
        $this->translations = $this->matches[0];

        return $this;
    }

    public function replace()
    {
        $isEmpty = false;
        $painter = new PainterService;

        foreach ($this->matches[0] as $key => $value) {
            if (empty($value)) {
                $isEmpty = true;
                break;
            }
            $formatedValue = $this->matches[1][$key];
            $keys = (in_array($formatedValue, array_keys($this->bag))) ? array_keys($this->bag[$formatedValue]) : [];
            $result = (!empty($keys)) ? end($keys) : $value;

            $this->translations[$key] = $painter->color('variable', $result);
        }

        $input = (!$isEmpty) ? str_replace($this->matches[0], $this->translations, $this->input) : $this->input;

        return $painter->color('question', $input);
    }

    public function setBag(array $bag): self
    {
        $this->bag = $bag;

        return $this;
    }

    public function setInput(string $input): self
    {
        $this->input = $input;

        return $this;
    }
}
