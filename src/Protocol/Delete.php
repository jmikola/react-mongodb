<?php

namespace Jmikola\React\MongoDB\Protocol;

class Delete implements RequestInterface
{
    use RequestTrait;

    private $flags;
    private $zero = MessageInterface::ZERO;
    private $namespace;
    private $selector;

    public function __construct($namespace, $selector, $flags = 0)
    {
        $this->namespace = $namespace;
        $this->selector = $selector;
        $this->flags = $flags;
    }

    public function getOpCode()
    {
        return MessageInterface::OP_DELETE;
    }

    protected function getMessageDataAfterHeader()
    {
        $data = pack(
            'Va*xV',
            $this->zero,
            $this->namespace,
            $this->flags
        );

        $data .= \MongoDB\BSON\fromPHP($this->selector);

        return $data;
    }
}
