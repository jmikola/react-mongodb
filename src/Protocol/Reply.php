<?php

namespace Jmikola\React\MongoDB\Protocol;

use UnderflowException;

class Reply implements MessageInterface
{
    private $header;
    private $responseFlags;
    private $cursorId;
    private $startingFrom;
    private $numberReturned;
    private $documents;

    public function __construct($data)
    {
        $header = new MessageHeader(substr($data, 0, MessageInterface::MSG_HEADER_SIZE));
        $offset = MessageInterface::MSG_HEADER_SIZE;

        if (strlen($data) !== $header->getMessageLength()) {
            throw new UnderflowException(sprintf('Reply expected %d bytes; %d given', $header->getMessageLength(), strlen($data)));
        }

        $this->header = $header;

        list(
            $this->responseFlags,
            $cursorId,
            $this->startingFrom,
            $this->numberReturned
        ) = array_values(unpack('VresponseFlags/a8cursorId/VstartingFrom/VnumberReturned', substr($data, $offset, 20)));

        $offset += 20;

        $this->documents = substr($data, $offset, $header->getMessageLength() - $offset);
    }

    public function getCursorId()
    {
        return $this->cursorId;
    }

    public function getDocumentsData()
    {
        return $this->documents;
    }

    public function getMessageLength()
    {
        return $this->header->getMessageLength();
    }

    public function getNumberReturned()
    {
        return $this->numberReturned;
    }

    public function getOpCode()
    {
        return MessageInterface::OP_REPLY;
    }

    public function getResponseFlags()
    {
        return $this->responseFlags;
    }

    public function getResponseTo()
    {
        return $this->header->getResponseTo();
    }

    public function getStartingFrom()
    {
        return $this->startingFrom;
    }

    public function isResponseTo($requestId)
    {
        return $this->header->getResponseTo() === $requestId;
    }
}
