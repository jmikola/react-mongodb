<?php

namespace Jmikola\React\MongoDB;

class BsonIterator implements \Iterator
{
    private $buffer;
    private $bufferOffset = 0;
    private $bufferLength;
    private $current;
    private $key = 0;

    public function __construct($data)
    {
        $this->buffer = $data;
        $this->bufferLength = strlen($data);
    }

    public function current()
    {
        return $this->current;
    }

    public function key()
    {
        return $this->key;
    }

    public function next()
    {
        ++$this->key;
        $this->current = null;
        $this->initCurrent();
    }

    public function rewind()
    {
        $this->key = 0;
        $this->bufferOffset = 0;
        $this->current = null;
        $this->initCurrent();
    }

    public function valid()
    {
        return $this->current !== null;
    }

    private function initCurrent()
    {
        if ($this->bufferLength === $this->bufferOffset) {
            return;
        }

        if ($this->bufferLength - $this->bufferOffset < 5) {
            throw new \UnderflowException(sprintf('Expected at least 5 bytes; %d remaining', $this->bufferLength - $this->bufferOffset));
        }

        list(, $documentLength) = unpack('V', substr($this->buffer, $this->bufferOffset, 4));

        if ($this->bufferLength - $this->bufferOffset < $documentLength) {
            throw new \UnderflowException(sprintf('Expected %d bytes; %d remaining', $documentLength, $this->bufferLength - $this->bufferOffset));
        }

        $this->current = \MongoDB\BSON\toPHP(substr($this->buffer, $this->bufferOffset, $documentLength));
        $this->bufferOffset += $documentLength;
    }
}
