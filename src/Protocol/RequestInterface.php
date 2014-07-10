<?php

namespace Jmikola\React\MongoDB\Protocol;

interface RequestInterface extends MessageInterface
{
    public function getMessageData($requestId);
}
