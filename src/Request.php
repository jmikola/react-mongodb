<?php

namespace Jmikola\React\MongoDB;

use Jmikola\React\MongoDB\Protocol\Reply;
use React\Promise\Deferred;

class Request extends Deferred
{
    private $requestId;

    public function __construct($requestId)
    {
        $this->requestId = $requestId;
    }

    public function getRequestId()
    {
        return $this->requestId;
    }

    public function handleReply(Reply $reply)
    {
        // TODO: Check reply for error and call Deferred::reject()

        $this->resolve($reply);
    }
}
