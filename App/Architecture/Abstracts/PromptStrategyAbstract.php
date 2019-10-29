<?php

namespace App\Architecture\Abstracts;

use App\Architecture\Interfaces\PromptStrategyInterface;
use App\Architecture\Service\TranslateService;
use App\Sequences\SequenceLoader;
use Seld\CliPrompt\CliPrompt;

abstract class PromptStrategyAbstract implements PromptStrategyInterface
{
    /**
     * @var array
     */
    public $sequences = [];

    /**
     * @var TranslateService
     */
    public $translate;

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
        $this->sequences = (new SequenceLoader($this->pattern))->list();
        $this->translate = new TranslateService();
    }

    public function orchestrate(): self
    {
        for ($i = 0; $i < count($this->sequences->list); ++$i) {
            $sequence = new \ArrayIterator($this->sequences->get($this->sequences->list[$i]));

            foreach ($sequence as $key => $question) {
                echo $this->translate->setInput($question)
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
                $this->classesBag[$this->pointer['type']][$answer] = ['methods' => []];
                $this->pointer['class'] = $answer;
            }
        }
    }
}
