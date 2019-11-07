<?php declare(strict_types=1);

namespace DesignPatterns\Behavioral\ChainOfResponsibilities\Responsible;

use DesignPatterns\Behavioral\ChainOfResponsibilities\Interviewer;
use Psr\Http\Message\RequestInterface;

class HttpInMemoryCacheInterviewer extends Interviewer
{
    /**
     * @var array
     */
    private $data;

    /**
     * @param array $data
     * @param Interviewer|null $successor
     */
    public function __construct(array $data, Interviewer $successor = null)
    {
        parent::__construct($successor);

        $this->data = $data;
    }

    /**
     * @param RequestInterface $request
     *
     * @return string|null
     */
    protected function processing(RequestInterface $request)
    {
        $key = sprintf(
            '%s?%s',
            $request->getUri()->getPath(),
            $request->getUri()->getQuery()
        );

        if ($request->getMethod() == 'GET' && isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
    }
}
