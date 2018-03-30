<?php

namespace Jmikola\React\MongoDB\Protocol;

class Insert implements RequestInterface
{
    use RequestTrait;

    private $flags;
    private $namespace;
    private $documents;

    public function __construct($namespace, $documents = null, $flags = 0)
    {
        if ($documents === null && (! is_array($documents) || ! is_object($documents))) {
            throw new \InvalidArgumentException(sprintf('Expected array or object for $documents; %s given', gettype($documents)));
        }

        $this->namespace = $namespace;
        $this->documents =  is_array($documents) ? $documents : [$documents];
        $this->flags = $flags;
    }

    public function getOpCode()
    {
        return MessageInterface::OP_INSERT;
    }

    protected function getMessageDataAfterHeader()
    {
        $data = pack(
            'Va*x',
            $this->flags,
            $this->namespace
        );

        foreach ($this->documents as $document) {
            $data .= \MongoDB\BSON\fromPHP($document);
        }

        return $data;
    }
}
