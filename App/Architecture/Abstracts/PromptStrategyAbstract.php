<?php

namespace App\Architecture\Abstracts;

use App\Architecture\Interfaces\PromptStrategyInterface;
use Seld\CliPrompt\CliPrompt;
use App\Controller\Index as BaseController;

abstract class PromptStrategyAbstract extends BaseController implements PromptStrategyInterface
{
    /**
     * @var array
     */
    public $sequences = [];

    /**
     * @var array
     */
    public $pointer = [
      'type' => '',
      'class' => '',
    ];

    /**
     * @var array
     */
    public $classesBag = [];

    public function __construct(string $pattern)
    {
        $this->sequences = $this->get('sequence.loader')->setPattern($pattern)->list();
    }

    public function orchestrate(): self
    {
        for ($i = 0; $i < count($this->sequences->list); ++$i) {
            $sequence = new \ArrayIterator($this->sequences->load($this->sequences->list[$i]));

            foreach ($sequence as $key => $question) {
                $translator = $this->get('translate.service');
                echo $translator->setInput($question)
                    ->setBag($this->classesBag)
                    ->identify()
                    ->replace();

                $answer = CliPrompt::prompt();

                if ('namespace' === $key && strpos($answer, '\\') === false) {
                    echo "Invalid namespace $answer, expected a valid namespace \n";
                    $sequence->rewind();
                }

                if (strpos($key, '_') !== false) {
                    $parts = explode('_', $key);

                    if ('reverse' === $parts[0]) {
                        $answer = (empty($answer)) ? 'n' : $answer;
                        if ('n' === strtolower($answer)) {
                            continue;
                        }

                        $iterator = (int) $parts[1];
                        $i = ($i === $iterator) ? --$i : $iterator - 1;
                        continue;
                    }
                } else {
                    $this->_fillBag($key, $answer);
                }
            }
        }

        return $this;
    }

    private function _fillBag(string $key, string $answer)
    {
        if ('method' === $key) {
            array_push($this->classesBag[$this->pointer['type']][$this->pointer['class']]['methods'], trim($answer));
        } else {
            $this->pointer['type'] = $key;
            if (!in_array($this->pointer['type'], array_keys($this->classesBag))) {
                $this->classesBag[$this->pointer['type']] = [];
            }

            if (!in_array($answer, array_keys($this->classesBag[$this->pointer['type']]))) {
                if ('namespace' === $this->pointer['type']) {
                    $this->classesBag[$this->pointer['type']]['general'] = $answer;

                    //Ex: for cabin, we will have folders __class__/Abstracts, __class__/Interfaces
                    //While for observer, we will have folders __class__/Subject. __class__/Observer
                    foreach ($this->sequences->convention as $k => $v) {
                        $this->classesBag[$this->pointer['type']][$k] = $answer.$v;
                    }
                } else {
                    $this->classesBag[$this->pointer['type']][$answer] = ['methods' => []];
                }

                $this->pointer['class'] = $answer;
            }
        }
    }
}

