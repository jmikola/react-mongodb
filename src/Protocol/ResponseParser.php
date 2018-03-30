<?php

namespace Jmikola\React\MongoDB\Protocol;

class ResponseParser
{
    private $buffer = '';

    public function pushAndGetParsed($data)
    {
        $this->buffer .= $data;

        return $this->getParsedMessages();
    }

    private function getParsedMessages()
    {
        $messages = [];

        while ($this->buffer !== '') {
            $message = $this->parseMessage();

            if ($message === null) {
                break;
            }

            $messages[] = $message;

            $this->buffer = substr($this->buffer, $message->getMessageLength());
        };

        return $messages;
    }

    private function parseMessage()
    {
        if (strlen($this->buffer) < 4) {
            return null;
        }

        list(, $messageLength) = unpack('V', $this->buffer);

        if (strlen($this->buffer) < $messageLength) {
            return null;
        }

        return new Reply(substr($this->buffer, 0, $messageLength));
    }
}
