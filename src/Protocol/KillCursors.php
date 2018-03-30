<?php

namespace Jmikola\React\MongoDB\Protocol;

class KillCursors implements RequestInterface
{
    use RequestTrait;

    private $cursorIds;

    public function __construct(array $cursorIds)
    {
        foreach ($cursorIds as $cursorId) {
            if (strlen($cursorId) !== 8) {
                throw new \InvalidArgumentException(sprintf('Expected 8-byte $cursorId; %d given', strlen($cursorId)));
            }
        }

        $this->cursorIds = $cursorIds;
    }

    public function getOpCode()
    {
        return MessageInterface::OP_KILL_CURSORS;
    }

    protected function getMessageDataAfterHeader()
    {
        return pack('VV', 0, count($this->cursorIds)) . implode($this->cursorIds);
    }
}
