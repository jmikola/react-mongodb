<?php

namespace Jmikola\React\MongoDB\Protocol;

class Query implements RequestInterface
{
    use RequestTrait;

    private $flags;
    private $namespace;
    private $numberToReturn;
    private $numberToSkip;
    private $query;
    private $selector;

    public function __construct($namespace, $query = null, $selector = null, $numberToSkip = 0, $numberToReturn = 0, $flags = 0)
    {
        if ($query !== null && ! is_array($query) && ! is_object($query)) {
            throw new \InvalidArgumentException(sprintf('Expected array or object for $query; %s given', gettype($query)));
        }

        if ($selector !== null && ! is_array($selector) && ! is_object($selector)) {
            throw new \InvalidArgumentException(sprintf('Expected array or object for $selector; %s given', gettype($selector)));
        }

        $this->namespace = $namespace;
        $this->query = $query ?: new \stdClass();
        $this->selector = $selector ?: null;
        $this->numberToSkip = $numberToSkip;
        $this->numberToReturn = $numberToReturn;
        $this->flags = $flags;
    }

    public function getOpCode()
    {
        return MessageInterface::OP_QUERY;
    }

    protected function getMessageDataAfterHeader()
    {
        $data = pack(
            'Va*xVVa*',
            $this->flags,
            $this->namespace,
            $this->numberToSkip,
            $this->numberToReturn,
            \MongoDB\BSON\fromPHP($this->query)
        );

        if ($this->selector !== null) {
            $data .= \MongoDB\BSON\fromPHP($this->selector);
        }

        return $data;
    }
}
