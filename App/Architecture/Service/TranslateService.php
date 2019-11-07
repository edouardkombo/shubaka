<?php

namespace App\Architecture\Service;

use App\Architecture\Interfaces\TranslateInterface;
use App\Controller\ServiceContainer;

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

    public function __construct(ServiceContainer $serviceContainer)
    {
        var_dump($serviceContainer);
        $this->painter = $serviceContainer->get('painter.service');
    }

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

        foreach ($this->matches[0] as $key => $value) {
            if (empty($value)) {
                $isEmpty = true;
                break;
            }
            $formatedValue = $this->matches[1][$key];
            $keys = (in_array($formatedValue, array_keys($this->bag))) ? array_keys($this->bag[$formatedValue]) : [];
            $result = (!empty($keys)) ? end($keys) : $value;

            $this->translations[$key] = $this->painter->color('variable', $result);
        }

        $input = (!$isEmpty) ? str_replace($this->matches[0], $this->translations, $this->input) : $this->input;
var_dump($this->painter);
        return $this->painter->color('question', $input);
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
