<?php

namespace Jmikola\React\MongoDB\Protocol;

class MessageHeader
{
    private $messageLength;
    private $opCode;
    private $requestId;
    private $responseTo;

    public function __construct($data)
    {
        if (strlen($data) !== MessageInterface::MSG_HEADER_SIZE) {
            throw new \UnderflowException(sprintf('MessageHeader expected %d bytes; %d given', MessageInterface::MSG_HEADER_SIZE, strlen($data)));
        }

        list(
            $this->messageLength,
            $this->requestId,
            $this->responseTo,
            $this->opCode
        ) = array_values(unpack('VmessageLength/VrequestId/VresponseTo/VopCode', $data));

        switch ($this->opCode) {
            case MessageInterface::OP_REPLY:
            case MessageInterface::OP_MSG:
            case MessageInterface::OP_UPDATE:
            case MessageInterface::OP_INSERT:
            case MessageInterface::OP_QUERY:
            case MessageInterface::OP_GET_MORE:
            case MessageInterface::OP_DELETE:
            case MessageInterface::OP_KILL_CURSORS:
                break;

            default:
                throw new \UnexpectedValueException(sprintf('Unexpected opCode: %d', $this->opCode));
        }
    }

    public function getMessageLength()
    {
        return $this->messageLength;
    }

    public function getOpCode()
    {
        return $this->opCode;
    }

    public function getRequestId()
    {
        return $this->requestId;
    }

    public function getResponseTo()
    {
        return $this->responseTo;
    }
}
