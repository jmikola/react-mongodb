<?php

namespace Jmikola\React\MongoDB\Protocol;

class RequestFactory
{
    private $requestId = 0;

    public function create(RequestInterface $request)
    {
        $data = $request->getMessageData();

        $header = pack('V4', MessageInterface::MSG_HEADER_SIZE + strlen($data), ++$this->requestId, 0, $request->getOpCode());

        return $header . $data;
    }
}
