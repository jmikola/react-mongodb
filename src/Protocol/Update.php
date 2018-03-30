<?php

namespace Jmikola\React\MongoDB\Protocol;

class Update implements RequestInterface
{
    use RequestTrait;

    private $flags;
    private $zero = MessageInterface::ZERO;
    private $namespace;
    private $selector;
    private $update;

    public function __construct($namespace, $selector, $update, $flags = 0)
    {
        $this->namespace = $namespace;
        $this->selector = $selector;
        $this->update = $update;
        $this->flags = $flags;
    }

    public function getOpCode()
    {
        return MessageInterface::OP_UPDATE;
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
        $data .= \MongoDB\BSON\fromPHP($this->update);

        return $data;
    }
}
