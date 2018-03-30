<?php

namespace Jmikola\React\MongoDB\Protocol;

class GetMore implements RequestInterface
{
    use RequestTrait;

    private $cursorId;
    private $namespace;
    private $numberToReturn;

    public function __construct($namespace, $cursorId, $numberToReturn = 0)
    {
        if (strlen($cursorId) !== 8) {
            throw new \InvalidArgumentException(sprintf('Expected 8-byte $cursorId; %d given', strlen($cursorId)));
        }

        $this->namespace = $namespace;
        $this->cursorId = $cursorId;
        $this->numberToReturn = $numberToReturn;
    }

    public function getOpCode()
    {
        return MessageInterface::OP_GET_MORE;
    }

    protected function getMessageDataAfterHeader()
    {
        return pack('Va*Va*', 0, $this->namespace, $this->numberToReturn, $this->cursorId);
    }
}
